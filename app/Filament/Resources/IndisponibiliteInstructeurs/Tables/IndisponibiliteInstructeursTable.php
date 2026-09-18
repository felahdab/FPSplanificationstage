<?php

namespace Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class IndisponibiliteInstructeursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('instructeur.nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('instructeur.prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date_debut')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('heure_debut')
                    ->label('Heure début')
                    ->placeholder('—'),

                TextColumn::make('date_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('heure_fin')
                    ->label('Heure fin')
                    ->placeholder('—'),

                IconColumn::make('journee_entiere')
                    ->label('Journée entière')
                    ->boolean(),

                TextColumn::make('motif')
                    ->label('Motif')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'conge' => 'Congé',
                            'mission' => 'Mission',
                            'formation' => 'Formation',
                            'service' => 'Service',
                            'absence' => 'Absence',
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
                    ->trueLabel('Actives uniquement')
                    ->falseLabel('Inactives uniquement')
                    ->placeholder('Toutes'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('date_debut', 'asc');
    }
}