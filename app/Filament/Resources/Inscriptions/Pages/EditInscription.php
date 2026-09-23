<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Inscriptions\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Modules\FPSplanificationstage\Filament\Resources\Inscriptions\InscriptionResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                ->schema([
                    FileUpload::make(
                        'derogation_document'
                    )
                        ->label(
                            'Document de dérogation accepté'
                        )
                        ->helperText(
                            'Facultatif — PDF, JPEG ou PNG (10 Mo maximum).'
                        )
                        ->disk('local')
                        ->directory(
                            'inscriptions/derogations'
                        )
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                        ])
                        ->maxSize(10240)
                        ->previewable(false),
                ])
                ->action(
                    function (
                        array $data
                    ): void {
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

                            'derogation_document' =>
                                $data[
                                    'derogation_document'
                                ]
                                ?? null,
                        ]);

                        $record->refresh();
                        $this->record->refresh();

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
                            'derogation_document',
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

            Action::make(
                'telechargerDerogation'
            )
                ->label(
                    'Télécharger la dérogation'
                )
                ->icon(
                    'heroicon-o-arrow-down-tray'
                )
                ->visible(
                    fn (): bool =>
                        filled(
                            $this
                                ->record
                                ->derogation_document
                        )
                )
                ->action(
                    function (): StreamedResponse {
                        $path =
                            $this
                                ->record
                                ->derogation_document;

                        abort_unless(
                            is_string($path)
                            && Storage::disk(
                                'local'
                            )->exists($path),
                            404
                        );

                        $extension =
                            pathinfo(
                                $path,
                                PATHINFO_EXTENSION
                            );

                        $nomFichier =
                            'derogation-'
                            . $this
                                ->record
                                ->code_inscription
                            . (
                                $extension !== ''
                                    ? '.' . $extension
                                    : ''
                            );

                        return Storage::disk(
                            'local'
                        )->download(
                            $path,
                            $nomFichier
                        );
                    }
                ),

            DeleteAction::make(),
        ];
    }
}
