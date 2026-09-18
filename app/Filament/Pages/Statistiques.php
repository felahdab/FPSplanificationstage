<?php

namespace Modules\PlanificationStages\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Modules\PlanificationStages\Models\BesoinFormation;
use Modules\PlanificationStages\Models\Inscription;
use Modules\PlanificationStages\Models\SessionStage;

class Statistiques extends Page
{
    protected string $view =
        'planificationstages::filament.pages.statistiques';

    protected static ?string $navigationLabel =
        'Statistiques';

    protected static string|\UnitEnum|null $navigationGroup =
        'Pilotage';

    protected static ?int $navigationSort =
        10;

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear =
            (int) now()->year;
    }

    public function getTitle(): string
    {
        return 'Statistiques';
    }

    public function previousYear(): void
    {
        $this->selectedYear--;
    }

    public function nextYear(): void
    {
        $this->selectedYear++;
    }

    public function currentYear(): void
    {
        $this->selectedYear =
            (int) now()->year;
    }

    public function availableYears(): array
    {
        $currentYear =
            (int) now()->year;

        $firstSession =
            SessionStage::query()
                ->orderBy('debut')
                ->first();

        $lastSession =
            SessionStage::query()
                ->orderByDesc('debut')
                ->first();

        $firstYear =
            $firstSession?->debut
                ?->year
            ?? $currentYear;

        $lastYear =
            $lastSession?->debut
                ?->year
            ?? $currentYear;

        $firstYear =
            min(
                $firstYear,
                $currentYear
            );

        $lastYear =
            max(
                $lastYear,
                $currentYear
            );

        $years = [];

        for (
            $year = $lastYear;
            $year >= $firstYear;
            $year--
        ) {
            $years[] = $year;
        }

        if (
            ! in_array(
                $this->selectedYear,
                $years,
                true
            )
        ) {
            $years[] =
                $this->selectedYear;

            rsort($years);
        }

        return $years;
    }

    public function statsData(): array
    {
        $yearStart =
            Carbon::create(
                $this->selectedYear,
                1,
                1,
                0,
                0,
                0
            )
                ->startOfDay();

        $yearEnd =
            $yearStart
                ->copy()
                ->endOfYear()
                ->endOfDay();

        $sessions =
            SessionStage::query()
                ->withCount([
                    'inscriptions as participants_count' =>
                        fn ($query) =>
                            $query->whereIn(
                                'statut',
                                [
                                    'attente_nemo',
                                    'confirmee',
                                    'attente_derogation',
                                ]
                            ),
                ])
                ->whereBetween(
                    'debut',
                    [
                        $yearStart,
                        $yearEnd,
                    ]
                )
                ->get();

        $sessionsAnnulees =
            $sessions
                ->where(
                    'statut',
                    'annulee'
                );

        $sessionsNonAnnulees =
            $sessions
                ->where(
                    'statut',
                    '!=',
                    'annulee'
                );

        $maintenant =
            now();

        $sessionsRealisees =
            $sessionsNonAnnulees
                ->filter(
                    fn (
                        SessionStage $session
                    ): bool =>
                        $session->fin !== null
                        && $session->fin->lt(
                            $maintenant
                        )
                );

        $sessionsAVenir =
            $sessionsNonAnnulees
                ->filter(
                    fn (
                        SessionStage $session
                    ): bool =>
                        $session->debut !== null
                        && $session->debut->gte(
                            $maintenant
                        )
                );

        $sessionsEnCours =
            $sessionsNonAnnulees
                ->filter(
                    fn (
                        SessionStage $session
                    ): bool =>
                        $session->debut !== null
                        && $session->fin !== null
                        && $session->debut->lte(
                            $maintenant
                        )
                        && $session->fin->gte(
                            $maintenant
                        )
                );

        $capaciteTotale =
            $sessionsNonAnnulees
                ->whereNotNull(
                    'capacite_max'
                )
                ->sum(
                    'capacite_max'
                );

        $placesOccupees =
            $sessionsNonAnnulees
                ->sum(
                    fn (
                        SessionStage $session
                    ): int =>
                        (int)
                        $session
                            ->participants_count
                );

        $placesRestantes =
            $sessionsNonAnnulees
                ->sum(
                    fn (
                        SessionStage $session
                    ): int =>
                        $session
                            ->capacite_max
                            !== null
                        ? max(
                            0,
                            (int)
                                $session
                                    ->capacite_max
                            - (int)
                                $session
                                    ->participants_count
                        )
                        : 0
                );

        $tauxRemplissage =
            $capaciteTotale > 0
                ? round(
                    (
                        $placesOccupees
                        / $capaciteTotale
                    ) * 100,
                    1
                )
                : 0;

        $inscriptionsBase =
            Inscription::query()
                ->whereHas(
                    'sessionStage',
                    fn ($query) =>
                        $query->whereBetween(
                            'debut',
                            [
                                $yearStart,
                                $yearEnd,
                            ]
                        )
                );

        $stagiairesConfirmes =
            (clone $inscriptionsBase)
                ->where(
                    'statut',
                    'confirmee'
                )
                ->count();

        $stagiairesReserves =
            (clone $inscriptionsBase)
                ->whereIn(
                    'statut',
                    [
                        'attente_nemo',
                        'confirmee',
                        'attente_derogation',
                    ]
                )
                ->count();

        $listeAttente =
            (clone $inscriptionsBase)
                ->where(
                    'statut',
                    'liste_attente'
                )
                ->count();

        $besoinsTotal =
            BesoinFormation::query()
                ->whereYear(
                    'created_at',
                    $this->selectedYear
                )
                ->count();

        $besoinsAPlanifier =
            BesoinFormation::query()
                ->whereYear(
                    'created_at',
                    $this->selectedYear
                )
                ->where(
                    'statut',
                    'a_planifier'
                )
                ->count();

        $besoinsPlanifies =
            BesoinFormation::query()
                ->whereYear(
                    'created_at',
                    $this->selectedYear
                )
                ->where(
                    'statut',
                    'planifie'
                )
                ->count();

        return [
            'year' =>
                $this->selectedYear,

            'sessions_programmees' =>
                $sessionsNonAnnulees
                    ->count(),

            'sessions_realisees' =>
                $sessionsRealisees
                    ->count(),

            'sessions_a_venir' =>
                $sessionsAVenir
                    ->count(),

            'sessions_en_cours' =>
                $sessionsEnCours
                    ->count(),

            'sessions_annulees' =>
                $sessionsAnnulees
                    ->count(),

            'stagiaires_reserves' =>
                $stagiairesReserves,

            'stagiaires_confirmes' =>
                $stagiairesConfirmes,

            'liste_attente' =>
                $listeAttente,

            'capacite_totale' =>
                $capaciteTotale,

            'places_occupees' =>
                $placesOccupees,

            'places_restantes' =>
                $placesRestantes,

            'taux_remplissage' =>
                $tauxRemplissage,

            'besoins_total' =>
                $besoinsTotal,

            'besoins_a_planifier' =>
                $besoinsAPlanifier,

            'besoins_planifies' =>
                $besoinsPlanifies,
        ];
    }
}