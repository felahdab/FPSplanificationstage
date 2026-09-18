<?php

namespace Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\IndisponibiliteInstructeurResource;
use Modules\PlanificationStages\Services\IndisponibiliteInstructeurImporter;
use Throwable;

class ListIndisponibiliteInstructeurs extends ListRecords
{
    protected static string $resource = IndisponibiliteInstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importIndisponibilites')
                ->label('Importer Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Importer les indisponibilités')
                ->modalDescription(
                    'Le fichier doit contenir un onglet nommé "Indisponibilités".'
                )
                ->modalSubmitActionLabel('Importer')
                ->schema([
                    FileUpload::make('fichier')
                        ->label('Fichier Excel')
                        ->disk('local')
                        ->directory('imports/indisponibilites')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(20480)
                        ->required()
                        ->helperText(
                            'Formats acceptés : .xlsx ou .xls — 20 Mo maximum.'
                        ),
                ])
                ->action(function (array $data): void {
                    $uploadedPath = $data['fichier'] ?? null;

                    if (is_array($uploadedPath)) {
                        $uploadedPath = $uploadedPath[0] ?? null;
                    }

                    if (! is_string($uploadedPath) || $uploadedPath === '') {
                        Notification::make()
                            ->title('Import impossible')
                            ->body(
                                'Aucun fichier valide n’a été sélectionné.'
                            )
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $absolutePath = Storage::disk('local')
                            ->path($uploadedPath);

                        $result = app(
                            IndisponibiliteInstructeurImporter::class
                        )->import($absolutePath);

                        $this->resetTable();

                        if ($result['analysees'] === 0) {
                            Notification::make()
                                ->title('Aucune donnée à importer')
                                ->body(
                                    'L’onglet "Indisponibilités" ne contient aucune ligne exploitable.'
                                )
                                ->warning()
                                ->send();

                            return;
                        }

                        $resume =
                            "{$result['analysees']} ligne(s) analysée(s)" .
                            "\n" .
                            "{$result['creees']} créée(s) • " .
                            "{$result['mises_a_jour']} mise(s) à jour • " .
                            "{$result['inchangees']} inchangée(s)";

                        if (count($result['erreurs']) > 0) {
                            $details = implode(
                                ' | ',
                                array_slice(
                                    $result['erreurs'],
                                    0,
                                    5
                                )
                            );

                            Notification::make()
                                ->title(
                                    'Import terminé avec avertissements'
                                )
                                ->body(
                                    $resume .
                                    "\n" .
                                    count($result['erreurs']) .
                                    " erreur(s)" .
                                    "\n" .
                                    $details
                                )
                                ->warning()
                                ->persistent()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Import terminé')
                            ->body($resume)
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Erreur pendant l’import')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    } finally {
                        Storage::disk('local')
                            ->delete($uploadedPath);
                    }
                }),

            CreateAction::make()
                ->label('Créer une indisponibilité'),
        ];
    }
}