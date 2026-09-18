<?php

namespace Modules\PlanificationStages\Filament\Resources\SessionStages\RelationManagers;

use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\PlanificationStages\Filament\Resources\Inscriptions\InscriptionResource;
use Modules\PlanificationStages\Models\Inscription;
use Modules\PlanificationStages\Models\SessionStage;

class InscriptionsRelationManager extends RelationManager
{
    protected static string $relationship =
        'inscriptions';

    protected static ?string $title =
        'Inscriptions';

    public function table(
        Table $table
    ): Table {
        /** @var SessionStage $session */
        $session =
            $this->getOwnerRecord();

        $reservees =
            $session->places_reservees;

        $restantes =
            $session->places_restantes;

        $listeAttente =
            $session
                ->inscriptions()
                ->where(
                    'statut',
                    'liste_attente'
                )
                ->count();

        if (
            $session->capacite_max
            === null
        ) {
            $heading =
                'Inscriptions — '
                . $reservees
                . ' place(s) réservée(s)'
                . ' — '
                . $listeAttente
                . ' en liste d’attente';
        } else {
            $heading =
                'Inscriptions — '
                . $reservees
                . ' / '
                . $session->capacite_max
                . ' places réservées'
                . ' — '
                . $restantes
                . ' restante(s)'
                . ' — '
                . $listeAttente
                . ' en liste d’attente';
        }

        return $table
            ->heading(
                $heading
            )
            ->headerActions([

                Action::make(
                    'nouvelleInscription'
                )
                    ->label(
                        'Nouvelle inscription'
                    )
                    ->icon(
                        'heroicon-o-user-plus'
                    )
                    ->color('primary')
                    ->url(
                        fn (): string =>
                            InscriptionResource::getUrl(
                                'create',
                                [
                                    'session_stage_id' =>
                                        $session->id,
                                ]
                            )
                    ),
            ])
            ->columns([

                TextColumn::make(
                    'code_inscription'
                )
                    ->label(
                        'Inscription'
                    )
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make(
                    'nom'
                )
                    ->label(
                        'Stagiaire'
                    )
                    ->formatStateUsing(
                        fn (
                            ?string $state,
                            Inscription $record
                        ): string =>
                            $record
                                ->nom_complet
                    )
                    ->searchable([
                        'nom',
                        'prenom',
                    ])
                    ->sortable(),

                TextColumn::make(
                    'grade'
                )
                    ->label('Grade')
                    ->placeholder('—'),

                TextColumn::make(
                    'unite'
                )
                    ->label(
                        'Bâtiment / unité'
                    )
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make(
                    'statut'
                )
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            ?string $state
                        ): string =>
                            match ($state) {
                                'attente_nemo' =>
                                    'Attente NEMO',

                                'confirmee' =>
                                    'Confirmée',

                                'attente_derogation' =>
                                    'Attente dérogation',

                                'liste_attente' =>
                                    'Liste d’attente',

                                'refusee' =>
                                    'Refusée',

                                'annulee' =>
                                    'Annulée',

                                default =>
                                    $state ?? '—',
                            }
                    )
                    ->color(
                        fn (
                            ?string $state
                        ): string =>
                            match ($state) {
                                'attente_nemo' =>
                                    'warning',

                                'confirmee' =>
                                    'success',

                                'attente_derogation' =>
                                    'info',

                                'liste_attente' =>
                                    'gray',

                                'refusee',
                                'annulee' =>
                                    'danger',

                                default =>
                                    'gray',
                            }
                    ),

                IconColumn::make(
                    'nemo_recu'
                )
                    ->label('NEMO')
                    ->boolean(),

                TextColumn::make(
                    'email'
                )
                    ->label('E-mail')
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),

                TextColumn::make(
                    'created_at'
                )
                    ->label(
                        'Inscrit le'
                    )
                    ->dateTime(
                        'd/m/Y H:i'
                    )
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),
            ])
            ->recordActions([

                Action::make(
                    'ouvrir'
                )
                    ->label('Ouvrir')
                    ->icon(
                        'heroicon-o-arrow-top-right-on-square'
                    )
                    ->url(
                        fn (
                            Inscription $record
                        ): string =>
                            InscriptionResource::getUrl(
                                'edit',
                                [
                                    'record' =>
                                        $record
                                            ->getKey(),
                                ]
                            )
                    ),
            ])
            ->defaultSort(
                'created_at',
                'asc'
            );
    }
}