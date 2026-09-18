<?php

namespace Modules\PlanificationStages\Filament\Resources\Salles\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SallesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('nom')
                    ->label('Salle')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('capacite')
                    ->label('Capacité')
                    ->sortable()
                    ->suffix(' pers.')
                    ->placeholder('—'),

                TextColumn::make('localisation')
                    ->label('Localisation')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('type_salle')
                    ->label('Type')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'cours' => 'Salle de cours',
                            'informatique' => 'Salle informatique',
                            'simulateur' => 'Simulateur',
                            'conference' => 'Salle de conférence',
                            'atelier' => 'Atelier',
                            'autre' => 'Autre',
                            default => $state ?? '—',
                        }
                    ),

                IconColumn::make('actif')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('actif')
                    ->label('État')
                    ->trueLabel('Salles actives')
                    ->falseLabel('Salles inactives')
                    ->placeholder('Toutes'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('nom', 'asc');
    }
}