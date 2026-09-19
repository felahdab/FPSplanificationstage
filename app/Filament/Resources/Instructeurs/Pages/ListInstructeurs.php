<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\InstructeurResource;
use Modules\FPSplanificationstage\Services\InstructeurStageImporter;
use Throwable;

class ListInstructeurs extends ListRecords
{
    protected static string $resource = InstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importInstructeurs')
                ->label('Importer Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Importer les instructeurs et leurs stages')
                ->modalDescription(
                    'Le fichier doit contenir les onglets "Instructeurs" et "Stages délivrés".'
                )
                ->modalSubmitActionLabel('Importer')
                ->schema([
                    FileUpload::make('fichier')
                        ->label('Fichier Excel')
                        ->disk('local')
                        ->directory('imports/instructeurs')
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

                    // Sécurité au cas où Filament renverrait un tableau.
                    if (is_array($uploadedPath)) {
                        $uploadedPath = $uploadedPath[0] ?? null;
                    }

                    if (! is_string($uploadedPath) || $uploadedPath === '') {
                        Notification::make()
                            ->title('Import impossible')
                            ->body('Aucun fichier valide n’a été sélectionné.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $absolutePath = Storage::disk('local')
                            ->path($uploadedPath);

                        $result = app(
                            InstructeurStageImporter::class
                        )->import($absolutePath);

                        $this->resetTable();

                        $resume =
                            "Instructeurs : " .
                            "{$result['instructeurs_crees']} créés • " .
                            "{$result['instructeurs_mis_a_jour']} mis à jour • " .
                            "{$result['instructeurs_inchanges']} inchangés" .
                            "\n" .
                            "Associations stages : " .
                            "{$result['associations_creees']} créées • " .
                            "{$result['associations_mises_a_jour']} mises à jour • " .
                            "{$result['associations_inchangees']} inchangées";

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
                                ->title('Import terminé avec avertissements')
                                ->body(
                                    $resume .
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
                ->label('Créer un instructeur'),
        ];
    }
}