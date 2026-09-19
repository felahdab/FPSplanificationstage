<?php

namespace Modules\FPSplanificationstage\Filament\Resources\SessionStages\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Modules\FPSplanificationstage\Filament\Resources\BesoinFormations\BesoinFormationResource;
use Modules\FPSplanificationstage\Filament\Resources\SessionStages\SessionStageResource;
use Modules\FPSplanificationstage\Services\SessionStageAlternativeFinder;
use Modules\FPSplanificationstage\Services\SessionStageConflictDetector;

class EditSessionStage extends EditRecord
{
    protected static string $resource =
        SessionStageResource::class;

    public function getSubheading():
        string|Htmlable|null
    {
        $besoin =
            $this->record
                ?->besoinFormation;

        if (! $besoin) {
            return null;
        }

        return sprintf(
            'Issue du besoin %s — %s',
            $besoin->code_besoin
                ?? 'sans numéro',
            $besoin->demandeur
                ?? 'demandeur non renseigné'
        );
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make(
                'ouvrirBesoin'
            )
                ->label(
                    'Ouvrir le besoin'
                )
                ->icon(
                    'heroicon-o-arrow-top-right-on-square'
                )
                ->color('primary')
                ->visible(
                    fn (): bool =>
                        $this
                            ->record
                            ?->besoinFormation
                        !== null
                )
                ->url(
                    function (): ?string {
                        $besoin =
                            $this
                                ->record
                                ?->besoinFormation;

                        if (! $besoin) {
                            return null;
                        }

                        return BesoinFormationResource::getUrl(
                            'edit',
                            [
                                'record' =>
                                    $besoin
                                        ->getKey(),
                            ]
                        );
                    }
                ),

            ViewAction::make(),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        if (
            ! empty(
                $data['debut']
            )
        ) {
            $debut =
                Carbon::parse(
                    $data['debut']
                );

            $data['debut_date'] =
                $debut->format(
                    'Y-m-d'
                );

            $data['debut_heure'] =
                $debut->format(
                    'H:i'
                );
        }

        if (
            ! empty(
                $data['fin']
            )
        ) {
            $fin =
                Carbon::parse(
                    $data['fin']
                );

            $data['fin_date'] =
                $fin->format(
                    'Y-m-d'
                );

            $data['fin_heure'] =
                $fin->format(
                    'H:i'
                );
        }

        $data[
            'suggestions_salles'
        ] = [];

        $data[
            'suggestions_dates'
        ] = [];

        return $data;
    }

    protected function beforeSave(): void
    {
        $data =
            $this->prepareDateTimeData(
                $this->data
            );

        $sessionId =
            (int) $this
                ->record
                ->getKey();

        $conflits = app(
            SessionStageConflictDetector::class
        )->detect(
            $data,
            $sessionId
        );

        if ($conflits === []) {
            return;
        }

        $suggestions = app(
            SessionStageAlternativeFinder::class
        )->find(
            $data,
            $sessionId
        );

        $this->data[
            'suggestions_salles'
        ] =
            $suggestions['salles'];

        $this->data[
            'suggestions_dates'
        ] =
            $suggestions['dates'];

        Notification::make()
            ->title(
                'Conflit de planification'
            )
            ->body(
                implode(
                    "\n",
                    $conflits
                )
                . "\n\nChoisissez une solution dans le bloc « Vérification du planning »."
            )
            ->danger()
            ->persistent()
            ->send();

        $this->halt();
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        return $this
            ->prepareDateTimeData(
                $data
            );
    }

    private function prepareDateTimeData(
        array $data
    ): array {
        if (
            ! empty(
                $data['debut_date']
            )
            && ! empty(
                $data['debut_heure']
            )
        ) {
            $dateDebut =
                Carbon::parse(
                    $data[
                        'debut_date'
                    ]
                )->format(
                    'Y-m-d'
                );

            $data['debut'] =
                Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $dateDebut
                    . ' '
                    . $data[
                        'debut_heure'
                    ]
                )->format(
                    'Y-m-d H:i:s'
                );
        }

        if (
            ! empty(
                $data['fin_date']
            )
            && ! empty(
                $data['fin_heure']
            )
        ) {
            $dateFin =
                Carbon::parse(
                    $data[
                        'fin_date'
                    ]
                )->format(
                    'Y-m-d'
                );

            $data['fin'] =
                Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $dateFin
                    . ' '
                    . $data[
                        'fin_heure'
                    ]
                )->format(
                    'Y-m-d H:i:s'
                );
        }

        unset(
            $data['debut_date'],
            $data['debut_heure'],
            $data['fin_date'],
            $data['fin_heure'],
            $data['suggestions_salles'],
            $data['suggestions_dates']
        );

        return $data;
    }
}