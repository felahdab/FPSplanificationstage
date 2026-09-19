<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Inscriptions\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Modules\FPSplanificationstage\Models\Inscription;
use Modules\FPSplanificationstage\Models\SessionStage;

class InscriptionsTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->columns([

                TextColumn::make(
                    'code_inscription'
                )
                    ->label(
                        'Inscription'
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make(
                    'sessionStage.code_session'
                )
                    ->label('Session')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make(
                    'sessionStage.stage.libelle_court'
                )
                    ->label('Stage')
                    ->wrap(),

                TextColumn::make(
                    'sessionStage.debut'
                )
                    ->label('Date')
                    ->date(
                        'd/m/Y'
                    )
                    ->sortable(),

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

                                'refusee' =>
                                    'danger',

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
                    'nemo_recu_at'
                )
                    ->label(
                        'Reçu le'
                    )
                    ->dateTime(
                        'd/m/Y H:i'
                    )
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault:
                            true
                    ),
            ])
            ->filters([

                SelectFilter::make(
                    'statut'
                )
                    ->label('Statut')
                    ->options([
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
                    ])
                    ->multiple(),

                SelectFilter::make(
                    'session_stage_id'
                )
                    ->label('Session')
                    ->options(
                        fn (): array =>
                            SessionStage::query()
                                ->orderBy(
                                    'debut'
                                )
                                ->pluck(
                                    'code_session',
                                    'id'
                                )
                                ->all()
                    ),
            ])
            ->recordActions([

                Action::make(
                    'promouvoir'
                )
                    ->label(
                        'Promouvoir'
                    )
                    ->icon(
                        'heroicon-o-arrow-up-circle'
                    )
                    ->color('primary')
                    ->visible(
                        fn (
                            Inscription $record
                        ): bool =>
                            $record
                                ->statut
                            === 'liste_attente'
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Promouvoir cette inscription'
                    )
                    ->modalDescription(
                        fn (
                            Inscription $record
                        ): string =>
                            'Vérifier qu’une place est disponible pour '
                            . $record
                                ->nom_complet
                            . ' et le sortir de la liste d’attente ?'
                    )
                    ->modalSubmitActionLabel(
                        'Promouvoir'
                    )
                    ->action(
                        function (
                            Inscription $record
                        ): void {
                            $result =
                                DB::transaction(
                                    function () use (
                                        $record
                                    ): array {
                                        /*
                                         * Verrouillage de la session
                                         * pour éviter que deux promotions
                                         * prennent la dernière place.
                                         */
                                        $session =
                                            SessionStage::query()
                                                ->whereKey(
                                                    $record
                                                        ->session_stage_id
                                                )
                                                ->lockForUpdate()
                                                ->first();

                                        if (! $session) {
                                            return [
                                                'success' =>
                                                    false,

                                                'message' =>
                                                    'La session associée est introuvable.',
                                            ];
                                        }

                                        /*
                                         * Capacité non renseignée :
                                         * promotion autorisée.
                                         */
                                        if (
                                            $session
                                                ->capacite_max
                                            !== null
                                        ) {
                                            $placesReservees =
                                                Inscription::query()
                                                    ->where(
                                                        'session_stage_id',
                                                        $session->id
                                                    )
                                                    ->whereIn(
                                                        'statut',
                                                        [
                                                            'attente_nemo',
                                                            'confirmee',
                                                            'attente_derogation',
                                                        ]
                                                    )
                                                    ->count();

                                            if (
                                                $placesReservees
                                                >= $session
                                                    ->capacite_max
                                            ) {
                                                return [
                                                    'success' =>
                                                        false,

                                                    'message' =>
                                                        'La session est toujours complète. Aucune promotion n’est possible.',
                                                ];
                                            }
                                        }

                                        /*
                                         * NEMO déjà reçu :
                                         * inscription confirmée.
                                         *
                                         * NEMO non reçu :
                                         * la place est réservée
                                         * mais le statut reste
                                         * Attente NEMO.
                                         */
                                        $nouveauStatut =
                                            $record
                                                ->nemo_recu
                                                ? 'confirmee'
                                                : 'attente_nemo';

                                        $record->update([
                                            'statut' =>
                                                $nouveauStatut,
                                        ]);

                                        return [
                                            'success' =>
                                                true,

                                            'statut' =>
                                                $nouveauStatut,

                                            'message' =>
                                                $nouveauStatut
                                                === 'confirmee'
                                                    ? 'L’inscription a été promue et confirmée.'
                                                    : 'L’inscription a été promue. La place est réservée dans l’attente du NEMO.',
                                        ];
                                    }
                                );

                            if (
                                ! $result[
                                    'success'
                                ]
                            ) {
                                Notification::make()
                                    ->title(
                                        'Promotion impossible'
                                    )
                                    ->body(
                                        $result[
                                            'message'
                                        ]
                                    )
                                    ->warning()
                                    ->persistent()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title(
                                    'Inscription promue'
                                )
                                ->body(
                                    $result[
                                        'message'
                                    ]
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make(
                    'nemoRecu'
                )
                    ->label(
                        'NEMO reçu'
                    )
                    ->icon(
                        'heroicon-o-check-circle'
                    )
                    ->color('success')
                    ->visible(
                        fn (
                            Inscription $record
                        ): bool =>
                            ! $record
                                ->nemo_recu
                            && ! in_array(
                                $record->statut,
                                [
                                    'annulee',
                                    'refusee',
                                ],
                                true
                            )
                    )
                    ->requiresConfirmation()
                    ->modalHeading(
                        'Confirmer la réception du NEMO'
                    )
                    ->modalDescription(
                        fn (
                            Inscription $record
                        ): string =>
                            'Confirmer la réception du NEMO pour '
                            . $record
                                ->nom_complet
                            . ' ?'
                    )
                    ->modalSubmitActionLabel(
                        'NEMO reçu'
                    )
                    ->action(
                        function (
                            Inscription $record
                        ): void {
                            $nouveauStatut =
                                $record
                                    ->statut;

                            /*
                             * Attente NEMO :
                             * devient confirmée.
                             *
                             * Liste d'attente :
                             * reste en liste d'attente.
                             * La promotion reste une
                             * décision manuelle.
                             */
                            if (
                                $record
                                    ->statut
                                === 'attente_nemo'
                            ) {
                                $nouveauStatut =
                                    'confirmee';
                            }

                            $record->update([
                                'nemo_recu' =>
                                    true,

                                'nemo_recu_at' =>
                                    now(),

                                'statut' =>
                                    $nouveauStatut,
                            ]);

                            Notification::make()
                                ->title(
                                    'NEMO enregistré'
                                )
                                ->body(
                                    $record
                                        ->nom_complet
                                    . ' : réception du NEMO enregistrée.'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make(
                    'deplacerSession'
                )
                    ->label('Déplacer')
                    ->icon(
                        'heroicon-o-arrows-right-left'
                    )
                    ->color('info')
                    ->visible(
                        fn (
                            Inscription $record
                        ): bool =>
                            ! in_array(
                                $record->statut,
                                [
                                    'annulee',
                                    'refusee',
                                ],
                                true
                            )
                            && $record
                                ->session_stage_id
                                !== null
                    )
                    ->modalHeading(
                        'Déplacer le stagiaire'
                    )
                    ->modalDescription(
                        fn (
                            Inscription $record
                        ): string =>
                            'Déplacer '
                            . $record->nom_complet
                            . ' vers une autre session du même stage.'
                    )
                    ->modalSubmitActionLabel(
                        'Déplacer'
                    )
                    ->schema([
                        \Filament\Forms\Components\Select::make(
                            'nouvelle_session_id'
                        )
                            ->label(
                                'Nouvelle session'
                            )
                            ->options(
                                function (
                                    Inscription $record
                                ): array {
                                    $sessionActuelle =
                                        SessionStage::query()
                                            ->find(
                                                $record
                                                    ->session_stage_id
                                            );

                                    if (
                                        ! $sessionActuelle
                                    ) {
                                        return [];
                                    }

                                    return SessionStage::query()
                                        ->with('stage')
                                        ->where(
                                            'stage_id',
                                            $sessionActuelle
                                                ->stage_id
                                        )
                                        ->whereKeyNot(
                                            $sessionActuelle
                                                ->id
                                        )
                                        ->whereNotIn(
                                            'statut',
                                            [
                                                'annulee',
                                                'terminee',
                                            ]
                                        )
                                        ->where(
                                            'fin',
                                            '>=',
                                            now()
                                                ->startOfDay()
                                        )
                                        ->orderBy(
                                            'debut'
                                        )
                                        ->get()
                                        ->mapWithKeys(
                                            function (
                                                SessionStage $session
                                            ): array {
                                                $date =
                                                    $session
                                                        ->debut
                                                        ?->format(
                                                            'd/m/Y H:i'
                                                        )
                                                    ?? 'Date non renseignée';

                                                if (
                                                    $session
                                                        ->capacite_max
                                                    === null
                                                ) {
                                                    $places =
                                                        'places illimitées';
                                                } else {
                                                    $places =
                                                        $session
                                                            ->places_reservees
                                                        . '/'
                                                        . $session
                                                            ->capacite_max
                                                        . ' place(s)';
                                                }

                                                $complet =
                                                    $session
                                                        ->estComplete()
                                                        ? ' — COMPLET'
                                                        : '';

                                                return [
                                                    $session->id =>
                                                        $session
                                                            ->code_session
                                                        . ' — '
                                                        . $date
                                                        . ' — '
                                                        . $places
                                                        . $complet,
                                                ];
                                            }
                                        )
                                        ->all();
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText(
                                'Seules les autres sessions du même stage sont proposées. Une session complète ne peut pas recevoir le stagiaire.'
                            ),
                    ])
                    ->action(
                        function (
                            array $data,
                            Inscription $record
                        ): void {
                            $destinationId =
                                (int) (
                                    $data[
                                        'nouvelle_session_id'
                                    ]
                                    ?? 0
                                );

                            if (
                                $destinationId <= 0
                            ) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'nouvelle_session_id' =>
                                        'Choisissez une session de destination.',
                                ]);
                            }

                            $resultat =
                                \Illuminate\Support\Facades\DB::transaction(
                                    function () use (
                                        $record,
                                        $destinationId
                                    ): array {
                                        $inscription =
                                            Inscription::query()
                                                ->lockForUpdate()
                                                ->findOrFail(
                                                    $record->id
                                                );

                                        $source =
                                            SessionStage::query()
                                                ->lockForUpdate()
                                                ->findOrFail(
                                                    $inscription
                                                        ->session_stage_id
                                                );

                                        $destination =
                                            SessionStage::query()
                                                ->lockForUpdate()
                                                ->findOrFail(
                                                    $destinationId
                                                );

                                        if (
                                            $source->id
                                            === $destination->id
                                        ) {
                                            throw \Illuminate\Validation\ValidationException::withMessages([
                                                'nouvelle_session_id' =>
                                                    'Le stagiaire est déjà inscrit à cette session.',
                                            ]);
                                        }

                                        if (
                                            $source->stage_id
                                            !== $destination->stage_id
                                        ) {
                                            throw \Illuminate\Validation\ValidationException::withMessages([
                                                'nouvelle_session_id' =>
                                                    'Le déplacement n’est autorisé qu’entre deux sessions du même stage.',
                                            ]);
                                        }

                                        if (
                                            in_array(
                                                $destination->statut,
                                                [
                                                    'annulee',
                                                    'terminee',
                                                ],
                                                true
                                            )
                                        ) {
                                            throw \Illuminate\Validation\ValidationException::withMessages([
                                                'nouvelle_session_id' =>
                                                    'Cette session ne peut plus recevoir de stagiaire.',
                                            ]);
                                        }

                                        if (
                                            $destination->fin
                                            && $destination
                                                ->fin
                                                ->lt(
                                                    now()
                                                )
                                        ) {
                                            throw \Illuminate\Validation\ValidationException::withMessages([
                                                'nouvelle_session_id' =>
                                                    'Impossible de déplacer un stagiaire vers une session terminée.',
                                            ]);
                                        }

                                        if (
                                            $inscription->email
                                        ) {
                                            $doublon =
                                                Inscription::query()
                                                    ->where(
                                                        'session_stage_id',
                                                        $destination->id
                                                    )
                                                    ->where(
                                                        'id',
                                                        '<>',
                                                        $inscription->id
                                                    )
                                                    ->where(
                                                        'email',
                                                        $inscription->email
                                                    )
                                                    ->whereNotIn(
                                                        'statut',
                                                        [
                                                            'annulee',
                                                            'refusee',
                                                        ]
                                                    )
                                                    ->exists();

                                            if ($doublon) {
                                                throw \Illuminate\Validation\ValidationException::withMessages([
                                                    'nouvelle_session_id' =>
                                                        'Ce stagiaire possède déjà une inscription sur la session choisie.',
                                                ]);
                                            }
                                        }

                                        $nouveauStatut =
                                            $inscription
                                                ->statut;

                                        /*
                                         * Une personne en liste d’attente
                                         * déplacée vers une session disponible
                                         * récupère un statut normal.
                                         */
                                        if (
                                            $nouveauStatut
                                            === 'liste_attente'
                                        ) {
                                            $nouveauStatut =
                                                $inscription
                                                    ->nemo_recu
                                                    ? 'confirmee'
                                                    : 'attente_nemo';
                                        }

                                        $statutsQuiReservent = [
                                            'attente_nemo',
                                            'confirmee',
                                            'attente_derogation',
                                        ];

                                        if (
                                            $destination
                                                ->capacite_max
                                            !== null
                                            && in_array(
                                                $nouveauStatut,
                                                $statutsQuiReservent,
                                                true
                                            )
                                        ) {
                                            $placesReservees =
                                                Inscription::query()
                                                    ->where(
                                                        'session_stage_id',
                                                        $destination->id
                                                    )
                                                    ->where(
                                                        'id',
                                                        '<>',
                                                        $inscription->id
                                                    )
                                                    ->whereIn(
                                                        'statut',
                                                        $statutsQuiReservent
                                                    )
                                                    ->lockForUpdate()
                                                    ->get(['id'])
                                                    ->count();

                                            if (
                                                $placesReservees
                                                >= $destination
                                                    ->capacite_max
                                            ) {
                                                throw \Illuminate\Validation\ValidationException::withMessages([
                                                    'nouvelle_session_id' =>
                                                        'La session choisie est complète. Le déplacement n’a pas été effectué.',
                                                ]);
                                            }
                                        }

                                        $trace =
                                            '['
                                            . now()->format(
                                                'd/m/Y H:i'
                                            )
                                            . '] Déplacé de '
                                            . $source
                                                ->code_session
                                            . ' vers '
                                            . $destination
                                                ->code_session
                                            . '.';

                                        $commentaire =
                                            trim(
                                                (
                                                    $inscription
                                                        ->commentaire
                                                        ? $inscription
                                                            ->commentaire
                                                            . PHP_EOL
                                                        : ''
                                                )
                                                . $trace
                                            );

                                        $inscription->update([
                                            'session_stage_id' =>
                                                $destination->id,

                                            'statut' =>
                                                $nouveauStatut,

                                            'commentaire' =>
                                                $commentaire,
                                        ]);

                                        return [
                                            'stagiaire' =>
                                                $inscription
                                                    ->nom_complet,

                                            'source' =>
                                                $source
                                                    ->code_session,

                                            'destination' =>
                                                $destination
                                                    ->code_session,
                                        ];
                                    }
                                );

                            Notification::make()
                                ->title(
                                    'Stagiaire déplacé'
                                )
                                ->body(
                                    $resultat[
                                        'stagiaire'
                                    ]
                                    . ' : '
                                    . $resultat[
                                        'source'
                                    ]
                                    . ' → '
                                    . $resultat[
                                        'destination'
                                    ]
                                )
                                ->success()
                                ->send();
                        }
                    ),

                EditAction::make(),
            ])
            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}