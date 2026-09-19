<?php

namespace Modules\FPSplanificationstage\Filament\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Modules\FPSplanificationstage\Models\AdmissionMessageTemplate;
use Modules\FPSplanificationstage\Models\Inscription;
use Modules\FPSplanificationstage\Models\SessionStage;
use Modules\FPSplanificationstage\Models\Stage;

class Admission extends Page
{
    protected string $view = 'fpsplanificationstage::filament.pages.admission';
    protected static ?string $navigationLabel = 'Admission';
    protected static string|\UnitEnum|null $navigationGroup = 'Inscriptions';
    protected static ?int $navigationSort = 20;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';

    public ?int $sessionId = null;
    public ?int $templateId = null;
    public ?int $templateStageId = null;
    public string $templateName = '';
    public string $subjectTemplate = '';
    public string $bodyTemplate = '';
    public string $admittedFormat = '{grade} {nom} {prenom} — {unite}';
    public string $refusedFormat = '{grade} {nom} {prenom} — {unite}';
    public string $finalSubject = '';
    public string $finalBody = '';

    /** @var array<int|string, string> */
    public array $candidateDecisions = [];

    public function getTitle(): string
    {
        return 'Admission';
    }

    public function mount(): void
    {
        $this->bodyTemplate =
            "Bonjour,\n\n"
            . "Veuillez trouver ci-dessous les résultats d'admission pour le stage {stage}, session {session}, prévu du {date_debut} au {date_fin}.\n\n"
            . "CANDIDATS ADMIS\n\n{admis}\n\n"
            . "CANDIDATS REFUSÉS\n\n{refuses}\n\n"
            . "Cordialement,";
    }

    public function updatedSessionId(): void
    {
        $this->candidateDecisions = [];
        $session = $this->selectedSession();

        if (! $session) {
            $this->templateStageId = null;
            return;
        }

        $this->templateStageId = $session->stage_id;

        foreach ($session->inscriptions as $inscription) {
            $this->candidateDecisions[$inscription->id] = $this->defaultDecision($inscription);
        }
    }

    public function newTemplate(): void
    {
        $this->templateId = null;
        $this->templateName = '';
        $this->templateStageId = $this->selectedSession()?->stage_id;
        $this->subjectTemplate = '';
        $this->bodyTemplate = '';
        $this->admittedFormat = '{grade} {nom} {prenom} — {unite}';
        $this->refusedFormat = '{grade} {nom} {prenom} — {unite}';

        Notification::make()->title('Nouveau modèle')->info()->send();
    }

    public function loadTemplate(): void
    {
        if (! $this->templateId) {
            Notification::make()->title('Aucun modèle sélectionné')->warning()->send();
            return;
        }

        $template = AdmissionMessageTemplate::query()->find($this->templateId);

        if (! $template) {
            Notification::make()->title('Modèle introuvable')->danger()->send();
            return;
        }

        $this->templateName = $template->nom;
        $this->templateStageId = $template->stage_id;
        $this->subjectTemplate = (string) ($template->objet ?? '');
        $this->bodyTemplate = (string) $template->corps;
        $this->admittedFormat = (string) $template->format_admis;
        $this->refusedFormat = (string) $template->format_refuse;

        Notification::make()->title('Modèle chargé')->success()->send();
    }

    public function saveTemplate(): void
    {
        $name = trim($this->templateName);

        if ($name === '' || trim($this->bodyTemplate) === '') {
            Notification::make()->title('Nom et corps du message requis')->danger()->send();
            return;
        }

        if (trim($this->admittedFormat) === '' || trim($this->refusedFormat) === '') {
            Notification::make()->title('Format candidat requis')->danger()->send();
            return;
        }

        $template = $this->templateId
            ? AdmissionMessageTemplate::query()->find($this->templateId)
            : null;

        $template ??= new AdmissionMessageTemplate();

        $template->fill([
            'stage_id' => $this->templateStageId,
            'nom' => $name,
            'objet' => trim($this->subjectTemplate) !== '' ? $this->subjectTemplate : null,
            'corps' => $this->bodyTemplate,
            'format_admis' => $this->admittedFormat,
            'format_refuse' => $this->refusedFormat,
            'actif' => true,
        ]);

        $template->save();
        $this->templateId = $template->id;

        Notification::make()->title('Modèle enregistré')->success()->send();
    }

    public function deleteTemplate(): void
    {
        if (! $this->templateId) {
            return;
        }

        AdmissionMessageTemplate::query()->whereKey($this->templateId)->delete();
        $this->templateId = null;
        $this->templateName = '';

        Notification::make()->title('Modèle supprimé')->success()->send();
    }

    public function generateMessage(): void
    {
        $session = $this->selectedSession();

        if (! $session) {
            Notification::make()->title('Sélectionne une session')->warning()->send();
            return;
        }

        $admitted = [];
        $refused = [];

        foreach ($session->inscriptions as $candidate) {
            $decision = $this->candidateDecisions[$candidate->id] ?? 'ignorer';

            if ($decision === 'admis') {
                $admitted[] = $this->formatCandidate($candidate, $this->admittedFormat);
            } elseif ($decision === 'refuse') {
                $refused[] = $this->formatCandidate($candidate, $this->refusedFormat);
            }
        }

        $variables = $this->sessionVariables($session);
        $variables['{admis}'] = implode("\n", $admitted);
        $variables['{refuses}'] = implode("\n", $refused);

        $this->finalSubject = strtr($this->subjectTemplate, $variables);
        $this->finalBody = strtr($this->bodyTemplate, $variables);

        Notification::make()
            ->title('Message généré')
            ->body(count($admitted) . ' admis — ' . count($refused) . ' refusé(s). Le résultat reste entièrement modifiable.')
            ->success()
            ->send();
    }

    public function sessionOptions(): array
    {
        return SessionStage::query()
            ->with('stage')
            ->whereNotIn('statut', ['annulee'])
            ->orderBy('debut', 'desc')
            ->limit(250)
            ->get()
            ->mapWithKeys(function (SessionStage $session): array {
                $stage = $session->stage?->libelle_court ?? 'Stage';
                $date = $session->debut?->format('d/m/Y') ?? '—';

                return [$session->id => $stage . ' — ' . $session->code_session . ' — ' . $date];
            })
            ->all();
    }

    public function stageOptions(): array
    {
        return ['' => 'Modèle générique']
            + Stage::query()->orderBy('libelle_court')->pluck('libelle_court', 'id')->all();
    }

    public function templateOptions(): array
    {
        $query = AdmissionMessageTemplate::query()
            ->with('stage')
            ->where('actif', true);

        $session = $this->selectedSession();

        if ($session) {
            $query->where(function ($builder) use ($session): void {
                $builder->whereNull('stage_id')->orWhere('stage_id', $session->stage_id);
            });
        }

        return $query
            ->orderByRaw('stage_id IS NULL DESC')
            ->orderBy('nom')
            ->get()
            ->mapWithKeys(function (AdmissionMessageTemplate $template): array {
                $scope = $template->stage?->libelle_court ?? 'Générique';
                return [$template->id => '[' . $scope . '] ' . $template->nom];
            })
            ->all();
    }

    public function candidates(): Collection
    {
        $session = $this->selectedSession();

        if (! $session) {
            return collect();
        }

        return $session->inscriptions
            ->sortBy(fn (Inscription $candidate): string => mb_strtolower(($candidate->nom ?? '') . ' ' . ($candidate->prenom ?? '')))
            ->values();
    }

    public function placeholderHelp(): array
    {
        return [
            'Session' => [
                '{stage}', '{libelle_long}', '{session}', '{date_debut}', '{date_fin}', '{salle}', '{admis}', '{refuses}',
            ],
            'Candidat' => [
                '{nom}', '{prenom}', '{grade}', '{brevet}', '{specialite}', '{matricule}', '{nid}', '{unite}', '{email}', '{statut}',
            ],
        ];
    }

    private function selectedSession(): ?SessionStage
    {
        if (! $this->sessionId) {
            return null;
        }

        return SessionStage::query()
            ->with(['stage', 'salle', 'inscriptions'])
            ->find($this->sessionId);
    }

    private function defaultDecision(Inscription $candidate): string
    {
        return match ($candidate->statut) {
            'confirmee' => 'admis',
            'refusee', 'annulee' => 'refuse',
            default => 'ignorer',
        };
    }

    private function formatCandidate(Inscription $candidate, string $format): string
    {
        return strtr($format, [
            '{nom}' => (string) ($candidate->nom ?? ''),
            '{prenom}' => (string) ($candidate->prenom ?? ''),
            '{grade}' => (string) ($candidate->grade ?? ''),
            '{brevet}' => (string) ($candidate->brevet ?? ''),
            '{specialite}' => (string) ($candidate->specialite ?? ''),
            '{matricule}' => (string) ($candidate->matricule ?? ''),
            '{nid}' => (string) ($candidate->nid ?? ''),
            '{unite}' => (string) ($candidate->unite ?? ''),
            '{email}' => (string) ($candidate->email ?? ''),
            '{statut}' => (string) ($candidate->statut ?? ''),
        ]);
    }

    private function sessionVariables(SessionStage $session): array
    {
        return [
            '{stage}' => (string) ($session->stage?->libelle_court ?? ''),
            '{libelle_long}' => (string) ($session->stage?->libelle_long ?? ''),
            '{session}' => (string) ($session->code_session ?? ''),
            '{date_debut}' => $this->formatDate($session->debut),
            '{date_fin}' => $this->formatDate($session->fin),
            '{salle}' => (string) ($session->salle?->nom ?? ''),
        ];
    }

    private function formatDate(mixed $value): string
    {
        if (! $value) {
            return '';
        }

        if ($value instanceof Carbon) {
            return $value->format('d/m/Y');
        }

        return Carbon::parse($value)->format('d/m/Y');
    }
}
