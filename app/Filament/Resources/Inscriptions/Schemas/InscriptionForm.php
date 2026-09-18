<?php

namespace Modules\PlanificationStages\Filament\Resources\Inscriptions\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\PlanificationStages\Models\Inscription;
use Modules\PlanificationStages\Models\SessionStage;

class InscriptionForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                Section::make(
                    'Session'
                )
                    ->schema([

                        Select::make(
                            'session_stage_id'
                        )
                            ->label(
                                'Session de stage'
                            )
                            ->options(
                                fn (): array =>
                                    SessionStage::query()
                                        ->with('stage')
                                        ->orderBy('debut')
                                        ->get()
                                        ->mapWithKeys(
                                            function (
                                                SessionStage $session
                                            ): array {
                                                $stage =
                                                    $session
                                                        ->stage
                                                        ?->libelle_court
                                                    ?? 'Stage';

                                                $debut =
                                                    $session
                                                        ->debut
                                                        ?->format(
                                                            'd/m/Y'
                                                        )
                                                    ?? '—';

                                                $places =
                                                    $session
                                                        ->capacite_max
                                                    !== null
                                                        ? $session
                                                            ->places_reservees
                                                            . '/'
                                                            . $session
                                                                ->capacite_max
                                                        : 'illimité';

                                                return [
                                                    $session->id =>
                                                        $session
                                                            ->code_session
                                                        . ' — '
                                                        . $stage
                                                        . ' — '
                                                        . $debut
                                                        . ' — '
                                                        . $places
                                                        . ' place(s)',
                                                ];
                                            }
                                        )
                                        ->all()
                            )
                            ->searchable()
                            ->required(),

                    ])
                    ->columns(1),

                Section::make(
                    'Stagiaire'
                )
                    ->schema([


                        Select::make(
                            'grade'
                        )
                            // GRADE_MENU_DEROULANT_V1_1_ADMIN
                            ->label('Grade')
                            ->options([
                                'MOT' => 'MOT',
                                'QM2' => 'QM2',
                                'QM1' => 'QM1',
                                'SM' => 'SM',
                                'MT' => 'MT',
                                'PM' => 'PM',
                                'MP' => 'MP',
                                'MJR' => 'MJR',
                            ])
                            ->placeholder(
                                'Sélectionner un grade'
                            ),

                        /*
                         * INSCRIPTION_IDENTITE_V1_ADMIN
                         */
                        TextInput::make(
                            'matricule'
                        )
                            ->label('Matricule')
                            ->maxLength(100),

                        TextInput::make(
                            'nid'
                        )
                            ->label('NID')
                            ->maxLength(100),

                        Select::make(
                            'brevet'
                        )
                            ->label('Brevet')
                            ->options([
                                'FEM' => 'FEM',
                                'BAT' => 'BAT',
                                'BS' => 'BS',
                                'BM' => 'BM',
                            ])
                            ->placeholder(
                                'Sélectionner un brevet'
                            ),

                        TextInput::make(
                            'specialite'
                        )
                            ->label('Spécialité')
                            ->maxLength(255),

                        TextInput::make(
                            'nom'
                        )
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make(
                            'prenom'
                        )
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make(
                            'unite'
                        )
                            ->label(
                                'Bâtiment / unité'
                            )
                            ->maxLength(255),

                        TextInput::make(
                            'email'
                        )
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),

                        TextInput::make(
                            'telephone'
                        )
                            ->label('Téléphone')
                            ->maxLength(255),

                    ])
                    ->columns(2),

                // PRESENCE_STAGE_V1
                Section::make(
                    'Participation'
                )
                    ->description(
                        'À renseigner après le déroulement du stage.'
                    )
                    ->schema([
                        Select::make(
                            'presence'
                        )
                            ->label(
                                'Présence au stage'
                            )
                            ->options([
                                'non_renseignee' =>
                                    'Non renseignée',

                                'present' =>
                                    'Présent',

                                'absent' =>
                                    'Absent',
                            ])
                            ->default(
                                'non_renseignee'
                            )
                            ->required(),
                    ]),
                Section::make(
                    'Prérequis déclarés'
                )
                    ->description(
                        'Réponses fournies par le stagiaire lors de son inscription.'
                    )
                    ->schema([

                        CheckboxList::make(
                            'prerequis_declares'
                        )
                            ->label(
                                'Prérequis remplis'
                            )
                            ->options(
                                function (
                                    ?Inscription $record
                                ): array {
                                    if (! $record) {
                                        return [];
                                    }

                                    $record->loadMissing(
                                        'sessionStage.stage.prerequis'
                                    );

                                    $prerequis =
                                        $record
                                            ->sessionStage
                                            ?->stage
                                            ?->prerequis;

                                    if (! $prerequis) {
                                        return [];
                                    }

                                    return $prerequis
                                        ->mapWithKeys(
                                            function (
                                                $prerequis
                                            ): array {
                                                $suffixe =
                                                    $prerequis
                                                        ->obligatoire
                                                        ? ' — obligatoire'
                                                        : ' — facultatif';

                                                return [
                                                    $prerequis->id =>
                                                        $prerequis
                                                            ->libelle
                                                        . $suffixe,
                                                ];
                                            }
                                        )
                                        ->all();
                                }
                            )
                            ->afterStateHydrated(
                                function (
                                    CheckboxList $component,
                                    ?Inscription $record
                                ): void {
                                    if (! $record) {
                                        $component->state([]);

                                        return;
                                    }

                                    $ids =
                                        $record
                                            ->prerequisReponses()
                                            ->where(
                                                'respecte',
                                                true
                                            )
                                            ->pluck(
                                                'prerequis_stage_id'
                                            )
                                            ->all();

                                    $component->state(
                                        $ids
                                    );
                                }
                            )
                            ->disabled()
                            ->dehydrated(false)
                            ->columns(1),

                        Textarea::make(
                            'prerequis_manquants'
                        )
                            ->label(
                                'Prérequis obligatoires manquants'
                            )
                            ->afterStateHydrated(
                                function (
                                    Textarea $component,
                                    ?Inscription $record
                                ): void {
                                    if (! $record) {
                                        $component->state(
                                            'Aucune information.'
                                        );

                                        return;
                                    }

                                    $record->loadMissing([
                                        'sessionStage.stage.prerequis',
                                        'prerequisReponses',
                                    ]);

                                    $prerequis =
                                        $record
                                            ->sessionStage
                                            ?->stage
                                            ?->prerequis;

                                    if (! $prerequis) {
                                        $component->state(
                                            'Aucun prérequis défini pour ce stage.'
                                        );

                                        return;
                                    }

                                    $idsRespectes =
                                        $record
                                            ->prerequisReponses
                                            ->where(
                                                'respecte',
                                                true
                                            )
                                            ->pluck(
                                                'prerequis_stage_id'
                                            )
                                            ->all();

                                    $manquants =
                                        $prerequis
                                            ->filter(
                                                fn ($item): bool =>
                                                    (bool) $item
                                                        ->obligatoire
                                                    && ! in_array(
                                                        $item->id,
                                                        $idsRespectes,
                                                        true
                                                    )
                                            )
                                            ->pluck(
                                                'libelle'
                                            )
                                            ->values();

                                    if (
                                        $manquants
                                            ->isEmpty()
                                    ) {
                                        $component->state(
                                            'Aucun prérequis obligatoire manquant.'
                                        );

                                        return;
                                    }

                                    $component->state(
                                        $manquants
                                            ->map(
                                                fn (
                                                    string $libelle
                                                ): string =>
                                                    '• '
                                                    . $libelle
                                            )
                                            ->implode(
                                                PHP_EOL
                                            )
                                    );
                                }
                            )
                            ->rows(4)
                            ->disabled()
                            ->dehydrated(false),

                    ])
                    ->columns(1),

                Section::make(
                    'Suivi de l’inscription'
                )
                    ->description(
                        'Informations internes réservées aux gestionnaires.'
                    )
                    ->schema([

                        Select::make(
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
                            ->default(
                                'attente_nemo'
                            )
                            ->required(),

                        Toggle::make(
                            'nemo_recu'
                        )
                            ->label(
                                'NEMO reçu'
                            )
                            ->default(false),

                        Toggle::make(
                            'derogation_demandee'
                        )
                            ->label(
                                'Dérogation demandée'
                            )
                            ->default(false)
                            ->live(),

                        Select::make(
                            'derogation_statut'
                        )
                            ->label(
                                'Statut dérogation'
                            )
                            ->options([
                                'en_attente' =>
                                    'En attente',

                                'acceptee' =>
                                    'Acceptée',

                                'refusee' =>
                                    'Refusée',
                            ])
                            ->visible(
                                fn ($get): bool =>
                                    (bool) $get(
                                        'derogation_demandee'
                                    )
                            ),

                        Textarea::make(
                            'derogation_motif'
                        )
                            ->label(
                                'Justification du stagiaire'
                            )
                            ->rows(3)
                            ->visible(
                                fn ($get): bool =>
                                    (bool) $get(
                                        'derogation_demandee'
                                    )
                            )
                            ->columnSpanFull(),

                        Textarea::make(
                            'commentaire'
                        )
                            ->label(
                                'Commentaire gestionnaire'
                            )
                            ->rows(4)
                            ->columnSpanFull(),

                        Hidden::make(
                            'source'
                        )
                            ->default(
                                'manuel'
                            ),

                    ])
                    ->columns(2),
            ]);
    }
}