<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Salles\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Modules\FPSplanificationstage\Services\SalleReservationWorkbookService;
use Throwable;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\FPSplanificationstage\Filament\Resources\Salles\SalleResource;

class ListSalles extends ListRecords
{
    protected static string $resource = SalleResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('importReservationsSalles')
                ->label('Importer réservations salles')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading(
                    'Importer le fichier de réservation des salles'
                )
                ->modalDescription(
                    'Le fichier importé devient le fichier maître pour l’export. '
                    . 'Les salles sont créées ou mises à jour et toutes les cellules '
                    . 'déjà occupées deviennent des indisponibilités de salles.'
                )
                ->modalSubmitActionLabel(
                    'Importer'
                )
                ->schema([
                    FileUpload::make('fichier')
                        ->label(
                            'Fichier de réservation'
                        )
                        ->disk('local')
                        ->directory(
                            'imports/reservations-salles'
                        )
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxSize(20480)
                        ->preserveFilenames()
                        ->required()
                        ->helperText(
                            'Format .xlsx — structure « resa salle ».'
                        ),
                ])
                ->action(
                    function (
                        array $data
                    ): void {
                        $uploadedPath =
                            $data[
                                'fichier'
                            ]
                            ?? null;

                        if (
                            ! is_string(
                                $uploadedPath
                            )
                            || $uploadedPath
                                === ''
                        ) {
                            Notification::make()
                                ->title(
                                    'Import impossible'
                                )
                                ->body(
                                    'Aucun fichier valide n’a été sélectionné.'
                                )
                                ->danger()
                                ->send();

                            return;
                        }

                        try {
                            $absolutePath =
                                Storage::disk(
                                    'local'
                                )
                                    ->path(
                                        $uploadedPath
                                    );

                            $result =
                                app(
                                    SalleReservationWorkbookService::class
                                )
                                    ->import(
                                        $absolutePath,
                                        basename(
                                            $uploadedPath
                                        )
                                    );

                            $this
                                ->resetTable();

                            $resume =
                                $result[
                                    'salles'
                                ]
                                . ' salle(s) • '
                                . $result[
                                    'salles_creees'
                                ]
                                . ' créée(s) • '
                                . $result[
                                    'salles_mises_a_jour'
                                ]
                                . ' mise(s) à jour • '
                                . $result[
                                    'occupations'
                                ]
                                . ' créneau(x) occupé(s) • année '
                                . $result[
                                    'annee'
                                ];

                            if (
                                $result[
                                    'salles_sans_capacite'
                                ]
                                > 0
                            ) {
                                $resume .=
                                    ' • '
                                    . $result[
                                        'salles_sans_capacite'
                                    ]
                                    . ' salle(s) sans capacité à renseigner';
                            }

                            Notification::make()
                                ->title(
                                    'Réservations de salles importées'
                                )
                                ->body(
                                    $resume
                                )
                                ->success()
                                ->persistent()
                                ->send();
                        } catch (
                            Throwable $exception
                        ) {
                            Notification::make()
                                ->title(
                                    'Erreur pendant l’import'
                                )
                                ->body(
                                    $exception
                                        ->getMessage()
                                )
                                ->danger()
                                ->persistent()
                                ->send();
                        } finally {
                            if (
                                is_string(
                                    $uploadedPath
                                )
                                && $uploadedPath
                                    !== ''
                            ) {
                                Storage::disk(
                                    'local'
                                )
                                    ->delete(
                                        $uploadedPath
                                    );
                            }
                        }
                    }
                ),

            Action::make('exportReservationsSalles')
                ->label('Exporter réservations salles')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(
                    function () {
                        try {
                            $path =
                                app(
                                    SalleReservationWorkbookService::class
                                )
                                    ->export();

                            return response()
                                ->download(
                                    $path,
                                    'resa salle - Skeletor - '
                                    . now()
                                        ->format(
                                            'Ymd-His'
                                        )
                                    . '.xlsx'
                                )
                                ->deleteFileAfterSend(
                                    true
                                );
                        } catch (
                            Throwable $exception
                        ) {
                            Notification::make()
                                ->title(
                                    'Export impossible'
                                )
                                ->body(
                                    $exception
                                        ->getMessage()
                                )
                                ->danger()
                                ->persistent()
                                ->send();

                            return null;
                        }
                    }
                ),
            CreateAction::make(),
        ];
    }
}
