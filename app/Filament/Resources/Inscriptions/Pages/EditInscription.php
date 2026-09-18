<?php

namespace Modules\PlanificationStages\Filament\Resources\Inscriptions\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Modules\PlanificationStages\Filament\Resources\Inscriptions\InscriptionResource;

class EditInscription extends EditRecord
{
    protected static string $resource =
        InscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make(
                'accepterDerogation'
            )
                ->label(
                    'Accepter la dérogation'
                )
                ->icon(
                    'heroicon-o-check-circle'
                )
                ->color('success')
                ->visible(
                    fn (): bool =>
                        (bool) $this
                            ->record
                            ->derogation_demandee
                        && $this
                            ->record
                            ->derogation_statut
                        === 'en_attente'
                )
                ->requiresConfirmation()
                ->modalHeading(
                    'Accepter la dérogation'
                )
                ->modalDescription(
                    'Le stagiaire pourra poursuivre son inscription malgré le ou les prérequis obligatoires non remplis.'
                )
                ->modalSubmitActionLabel(
                    'Accepter'
                )
                ->action(
                    function (): void {
                        $record =
                            $this
                                ->record
                                ->fresh();

                        $nouveauStatut =
                            $record
                                ->nemo_recu
                                ? 'confirmee'
                                : 'attente_nemo';

                        $record->update([
                            'derogation_statut' =>
                                'acceptee',

                            'statut' =>
                                $nouveauStatut,
                        ]);

                        $record->refresh();

                        /*
                         * Le modèle vérifie également
                         * la capacité de la session.
                         * Si celle-ci est complète,
                         * le stagiaire est placé en
                         * liste d'attente.
                         */
                        if (
                            $record->statut
                            === 'liste_attente'
                        ) {
                            Notification::make()
                                ->title(
                                    'Dérogation acceptée'
                                )
                                ->body(
                                    'La dérogation est acceptée, mais la session est complète : le stagiaire a été placé en liste d’attente.'
                                )
                                ->warning()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(
                                    'Dérogation acceptée'
                                )
                                ->body(
                                    $record
                                        ->nemo_recu
                                        ? 'Le stagiaire est maintenant confirmé.'
                                        : 'Le stagiaire est maintenant en attente du NEMO.'
                                )
                                ->success()
                                ->send();
                        }

                        $this->refreshFormData([
                            'statut',
                            'derogation_statut',
                        ]);
                    }
                ),

            Action::make(
                'refuserDerogation'
            )
                ->label(
                    'Refuser la dérogation'
                )
                ->icon(
                    'heroicon-o-x-circle'
                )
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        (bool) $this
                            ->record
                            ->derogation_demandee
                        && $this
                            ->record
                            ->derogation_statut
                        === 'en_attente'
                )
                ->requiresConfirmation()
                ->modalHeading(
                    'Refuser la dérogation'
                )
                ->modalDescription(
                    'L’inscription sera refusée car les prérequis obligatoires ne sont pas remplis.'
                )
                ->modalSubmitActionLabel(
                    'Refuser la dérogation'
                )
                ->action(
                    function (): void {
                        $this
                            ->record
                            ->update([
                                'derogation_statut' =>
                                    'refusee',

                                'statut' =>
                                    'refusee',
                            ]);

                        Notification::make()
                            ->title(
                                'Dérogation refusée'
                            )
                            ->body(
                                'L’inscription a été placée au statut Refusée.'
                            )
                            ->danger()
                            ->send();

                        $this->refreshFormData([
                            'statut',
                            'derogation_statut',
                        ]);
                    }
                ),

            DeleteAction::make(),
        ];
    }
}