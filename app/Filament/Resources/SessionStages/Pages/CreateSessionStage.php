<?php

namespace Modules\PlanificationStages\Filament\Resources\SessionStages\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\PlanificationStages\Filament\Resources\SessionStages\SessionStageResource;
use Modules\PlanificationStages\Services\SessionStageAlternativeFinder;
use Modules\PlanificationStages\Services\SessionStageConflictDetector;

class CreateSessionStage extends CreateRecord
{
    protected static string $resource =
        SessionStageResource::class;

    public function mount(): void
    {
        parent::mount();

        $date = request()->query('date');

        if (! $date) {
            return;
        }

        try {
            $date = Carbon::parse(
                $date
            )->format('Y-m-d');
        } catch (\Throwable) {
            return;
        }

        /*
         * Heure éventuellement transmise
         * depuis le planning semaine.
         */
        $heureDemandee = request()->query(
            'heure',
            '08:00'
        );

        $heuresAutorisees = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
        ];

        if (! in_array(
            $heureDemandee,
            $heuresAutorisees,
            true
        )) {
            $heureDemandee = '08:00';
        }

        /*
         * Depuis le planning :
         * - date déjà choisie
         * - heure de début choisie en vue semaine
         * - fin par défaut à 16:00
         */
        $this->form->fill([
            'statut' =>
                'brouillon',

            'debut_date' =>
                $date,

            'debut_heure' =>
                $heureDemandee,

            'fin_date' =>
                $date,

            'fin_heure' =>
                '16:00',

            'salle_forcee' =>
                false,

            'suggestions_salles' =>
                [],

            'suggestions_dates' =>
                [],
        ]);
    }

    protected function beforeCreate(): void
    {
        $data = $this->prepareDateTimeData(
            $this->data
        );

        $conflits = app(
            SessionStageConflictDetector::class
        )->detect(
            $data
        );

        if ($conflits === []) {
            return;
        }

        $suggestions = app(
            SessionStageAlternativeFinder::class
        )->find(
            $data
        );

        $this->data[
            'suggestions_salles'
        ] = $suggestions['salles'];

        $this->data[
            'suggestions_dates'
        ] = $suggestions['dates'];

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

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        return $this->prepareDateTimeData(
            $data
        );
    }

    private function prepareDateTimeData(
        array $data
    ): array {
        /*
         * Date + heure de début
         * => datetime SQL.
         */
        if (
            ! empty(
                $data['debut_date']
            )
            && ! empty(
                $data['debut_heure']
            )
        ) {
            $dateDebut = Carbon::parse(
                $data['debut_date']
            )->format('Y-m-d');

            $data['debut'] =
                Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $dateDebut
                    . ' '
                    . $data['debut_heure']
                )->format(
                    'Y-m-d H:i:s'
                );
        }

        /*
         * Date + heure de fin
         * => datetime SQL.
         */
        if (
            ! empty(
                $data['fin_date']
            )
            && ! empty(
                $data['fin_heure']
            )
        ) {
            $dateFin = Carbon::parse(
                $data['fin_date']
            )->format('Y-m-d');

            $data['fin'] =
                Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $dateFin
                    . ' '
                    . $data['fin_heure']
                )->format(
                    'Y-m-d H:i:s'
                );
        }

        /*
         * Ces champs servent uniquement
         * au formulaire.
         */
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