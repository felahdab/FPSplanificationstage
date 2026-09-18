<?php

namespace Modules\PlanificationStages\Filament\Resources\Stagiaires\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\PlanificationStages\Models\Stagiaire;

class StagiairesTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Stagiaire')
                    ->formatStateUsing(
                        fn (
                            ?string $state,
                            Stagiaire $record
                        ): string =>
                            $record
                                ->nom_complet
                    )
                    ->searchable([
                        'nom',
                        'prenom',
                        'nid',
                        'matricule',
                    ])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nid')
                    ->label('NID')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make(
                    'matricule'
                )
                    ->label('Matricule')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->placeholder('—'),

                TextColumn::make('brevet')
                    ->label('Brevet')
                    ->placeholder('—'),

                TextColumn::make(
                    'specialite'
                )
                    ->label('Spécialité')
                    ->placeholder('—'),

                TextColumn::make('unite')
                    ->label(
                        'Bâtiment / unité'
                    )
                    ->searchable()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Historique'),
            ])
            ->defaultSort(
                'nom',
                'asc'
            );
    }
}
