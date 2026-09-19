<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stages\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code_stage')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('libelle_court')
                    ->label('Intitulé du stage')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('fif_generation')
                    ->label('FIF')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state): string => match ($state) {
                            'nouvelle' => 'Nouvelle',
                            'ancienne' => 'Ancienne',
                            default => '—',
                        }
                    )
                    ->color(
                        fn ($state): string => match ($state) {
                            'nouvelle' => 'success',
                            'ancienne' => 'warning',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('centre_formation')
                    ->label('Centre de formation')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non renseigné'),

                TextColumn::make('duree_jours')
                    ->label('Durée')
                    ->formatStateUsing(function ($state): string {
                        if ($state === null || $state === '') {
                            return '—';
                        }

                        $duree = (float) $state;

                        if ($duree === 0.5) {
                            return '½ journée';
                        }

                        if ($duree === 1.0) {
                            return '1 jour';
                        }

                        $valeur = rtrim(
                            rtrim(
                                number_format($duree, 1, ',', ''),
                                '0'
                            ),
                            ','
                        );

                        return $valeur . ' jours';
                    })
                    ->sortable(),

                TextColumn::make('capacite_min')
                    ->label('Capa. min')
                    ->numeric()
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('capacite_max')
                    ->label('Capa. max')
                    ->numeric()
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('prerequis_count')
                    ->label('Prérequis')
                    ->counts('prerequis')
                    ->alignCenter(),

                TextColumn::make('fif_imported_at')
                    ->label('Import FIF')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modification')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('fif_generation')
                    ->label('Génération FIF')
                    ->options([
                        'nouvelle' => 'Nouvelle génération',
                        'ancienne' => 'Ancienne génération',
                    ]),

                TernaryFilter::make('actif')
                    ->label('Statut du stage')
                    ->placeholder('Tous les stages')
                    ->trueLabel('Stages actifs')
                    ->falseLabel('Stages inactifs'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Voir'),

                EditAction::make()
                    ->label('Modifier'),
            ])
            ->defaultSort('libelle_court', 'asc');
    }
}
