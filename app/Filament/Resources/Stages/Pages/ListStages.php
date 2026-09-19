<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stages\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Modules\FPSplanificationstage\Filament\Resources\Stages\StageResource;
use Modules\FPSplanificationstage\Services\FifStageImporter;
use Throwable;

class ListStages extends ListRecords
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importFif')
                ->label('Importer une FIF')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Importer une fiche d’identité de formation')
                ->modalDescription(
                    'Sélectionnez une FIF au format Excel. ' .
                    'Le modèle est détecté automatiquement : ' .
                    'ancienne génération ou nouvelle génération.'
                )
                ->modalSubmitActionLabel('Importer la FIF')
                ->schema([
                    FileUpload::make('fichier')
                        ->label('Fichier FIF')
                        ->disk('local')
                        ->directory('imports/fif')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(20480)
                        ->required()
                        ->helperText(
                            'Formats acceptés : .xlsx ou .xls — ' .
                            'ancienne et nouvelle génération — 20 Mo maximum.'
                        ),
                ])
                ->action(function (array $data): void {
                    $uploadedPath = $data['fichier'] ?? null;

                    if (
                        ! is_string($uploadedPath)
                        || $uploadedPath === ''
                    ) {
                        Notification::make()
                            ->title('Import impossible')
                            ->body(
                                'Aucun fichier FIF valide n’a été sélectionné.'
                            )
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $absolutePath =
                            Storage::disk('local')
                                ->path($uploadedPath);

                        $result = app(
                            FifStageImporter::class
                        )->import($absolutePath);

                        $this->resetTable();

                        $resume =
                            'Type détecté : ' .
                            $result['generation_label'] .
                            ' • ' .
                            $result['code_stage'] .
                            ' • fiche ' .
                            $result['action'] .
                            ' • ' .
                            $result['prerequis'] .
                            ' prérequis • ' .
                            $result['modules'] .
                            ' module(s)';

                        if (
                            count($result['warnings']) > 0
                        ) {
                            Notification::make()
                                ->title(
                                    'FIF importée avec avertissement'
                                )
                                ->body(
                                    $resume .
                                    ' — ' .
                                    implode(
                                        ' | ',
                                        $result['warnings']
                                    )
                                )
                                ->warning()
                                ->persistent()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('FIF importée')
                            ->body($resume)
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Erreur pendant l’import FIF')
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
                ->label('Créer un stage'),
        ];
    }
}
