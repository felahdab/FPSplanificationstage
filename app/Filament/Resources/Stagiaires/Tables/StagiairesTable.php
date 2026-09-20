<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stagiaires\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\RH\Models\Marin;

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
                            Marin $record
                        ): string =>
                            trim($record->nom . ' ' . $record->prenom)
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

                TextColumn::make('grade.libelle_court')
                    ->label('Grade')
                    ->placeholder('—'),

                TextColumn::make('brevet.libelle_court')
                    ->label('Brevet')
                    ->placeholder('—'),

                TextColumn::make('specialite.libelle_court')
                    ->label('Spécialité')
                    ->placeholder('—'),

                TextColumn::make('unite.libelle_court')
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
