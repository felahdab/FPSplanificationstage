<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stagiaires\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StagiairesTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->description(
                'Cette liste reprend les marins du module RH. Ouvrez un marin pour consulter ses inscriptions et ses participations aux stages.'
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make('grade.libelle_court')
                    ->label('Grade')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('brevet.libelle_court')
                    ->label('Brevet')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('specialite.libelle_court')
                    ->label('Spécialité')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unite.libelle_court')
                    ->label('Unité')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('matricule')
                    ->label('Matricule')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make('nid')
                    ->label('NID')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),
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
