<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StageInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make(
                    'Identification du stage'
                )
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'code_stage'
                        )
                            ->label('Code interne')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make(
                            'numero_externe'
                        )
                            ->label('Numéro catalogue')
                            ->placeholder('—'),

                        TextEntry::make(
                            'libelle_court'
                        )
                            ->label('Libellé court')
                            ->weight('bold')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make(
                            'libelle_long'
                        )
                            ->label('Libellé long')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make(
                            'appellation_chorus'
                        )
                            ->label('Appellation CHORUS')
                            ->placeholder('—'),

                        TextEntry::make(
                            'centre_formation'
                        )
                            ->label('Centre de formation')
                            ->placeholder('—'),

                        TextEntry::make(
                            'typologie'
                        )
                            ->label('Typologie')
                            ->placeholder('—'),

                        TextEntry::make(
                            'branche'
                        )
                            ->label('Branche')
                            ->placeholder('—'),

                        TextEntry::make(
                            'raf'
                        )
                            ->label('RAF')
                            ->placeholder('—'),

                        TextEntry::make(
                            'adc'
                        )
                            ->label('ADC')
                            ->placeholder('—'),
                    ]),

                Section::make(
                    'FIF — Identification'
                )
                    ->description(
                        'Informations issues de la Fiche d’Information Formation.'
                    )
                    ->visible(
                        fn ($record): bool =>
                            filled(
                                $record?->fif_generation
                            )
                    )
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'fif_generation'
                        )
                            ->label('Génération FIF')
                            ->badge()
                            ->formatStateUsing(
                                fn ($state): string =>
                                    match ($state) {
                                        'nouvelle' =>
                                            'Nouvelle génération',
                                        'ancienne' =>
                                            'Ancienne génération',
                                        default =>
                                            'Non renseignée',
                                    }
                            )
                            ->color(
                                fn ($state): string =>
                                    match ($state) {
                                        'nouvelle' =>
                                            'success',
                                        'ancienne' =>
                                            'warning',
                                        default =>
                                            'gray',
                                    }
                            ),

                        TextEntry::make(
                            'service_emetteur'
                        )
                            ->label('Service émetteur')
                            ->placeholder('—'),

                        TextEntry::make(
                            'intitule_formation'
                        )
                            ->label(
                                'Intitulé de la formation'
                            )
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make(
                            'si_enregistrement_qualification'
                        )
                            ->label(
                                'SI d’enregistrement / qualification'
                            )
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make(
                            'echelle_grades'
                        )
                            ->label(
                                'Échelle de grades / population'
                            )
                            ->placeholder('—'),

                        TextEntry::make(
                            'niveau_brevet'
                        )
                            ->label('Niveau / brevet')
                            ->placeholder('—'),

                        TextEntry::make(
                            'lieux_formation'
                        )
                            ->label(
                                'Lieu(x) de formation'
                            )
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),

                Section::make(
                    'Organisation du stage'
                )
                    ->columns(3)
                    ->schema([
                        TextEntry::make(
                            'duree_jours'
                        )
                            ->label('Durée')
                            ->formatStateUsing(
                                function ($state): string {
                                    if (
                                        $state === null
                                        || $state === ''
                                    ) {
                                        return '—';
                                    }

                                    $value =
                                        (float) $state;

                                    if ($value === 0.5) {
                                        return '0,5 jour';
                                    }

                                    if ($value === 1.0) {
                                        return '1 jour';
                                    }

                                    $formatted =
                                        rtrim(
                                            rtrim(
                                                number_format(
                                                    $value,
                                                    1,
                                                    ',',
                                                    ''
                                                ),
                                                '0'
                                            ),
                                            ','
                                        );

                                    return
                                        $formatted .
                                        ' jours';
                                }
                            ),

                        TextEntry::make(
                            'nb_sessions_annuelles'
                        )
                            ->label(
                                'Sessions / an'
                            )
                            ->placeholder('—'),

                        IconEntry::make(
                            'actif'
                        )
                            ->label('Stage actif')
                            ->boolean(),

                        TextEntry::make(
                            'capacite_min'
                        )
                            ->label(
                                'Capacité minimale'
                            )
                            ->placeholder('—')
                            ->suffix(' pers.'),

                        TextEntry::make(
                            'capacite_max'
                        )
                            ->label(
                                'Capacité maximale'
                            )
                            ->placeholder('—')
                            ->suffix(' pers.'),

                        TextEntry::make(
                            'sallePreferentielle.nom'
                        )
                            ->label(
                                'Salle préférentielle'
                            )
                            ->placeholder('—'),
                    ]),

                Section::make(
                    'Population concernée'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasLegacyPopulationData(
                                $record
                            )
                    )
                    ->columns(3)
                    ->schema([
                        IconEntry::make(
                            'ouvert_off'
                        )
                            ->label('OFF')
                            ->boolean(),

                        IconEntry::make(
                            'ouvert_om'
                        )
                            ->label('OM')
                            ->boolean(),

                        IconEntry::make(
                            'ouvert_qmm_mo'
                        )
                            ->label('QMM / MO')
                            ->boolean(),

                        IconEntry::make(
                            'ouverture_etrangers'
                        )
                            ->label(
                                'Ouvert aux étrangers'
                            )
                            ->boolean(),

                        IconEntry::make(
                            'ouverture_vca'
                        )
                            ->label('Ouverture VCA')
                            ->boolean(),

                        IconEntry::make(
                            'ouverture_vae'
                        )
                            ->label('Ouverture VAE')
                            ->boolean(),

                        TextEntry::make(
                            'autres_beneficiaires'
                        )
                            ->label(
                                'Autres bénéficiaires'
                            )
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),

                Section::make(
                    'Prérequis'
                )
                    ->description(
                        'Conditions associées à l’inscription au stage.'
                    )
                    ->schema([
                        RepeatableEntry::make(
                            'prerequis'
                        )
                            ->label(
                                'Liste des prérequis'
                            )
                            ->schema([
                                TextEntry::make(
                                    'libelle'
                                )
                                    ->label('Prérequis')
                                    ->columnSpanFull()
                                    ->placeholder('—'),

                                IconEntry::make(
                                    'obligatoire'
                                )
                                    ->label(
                                        'Obligatoire'
                                    )
                                    ->boolean(),

                                IconEntry::make(
                                    'actif'
                                )
                                    ->label('Actif')
                                    ->boolean(),

                                TextEntry::make(
                                    'source'
                                )
                                    ->label('Source')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn ($state): string =>
                                            match ($state) {
                                                'fif' => 'FIF',
                                                'excel' => 'Excel',
                                                'manuel' => 'Manuel',
                                                default =>
                                                    (string) ($state ?? '—'),
                                            }
                                    ),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Finalités de la formation'
                )
                    ->visible(
                        fn ($record): bool =>
                            filled(
                                $record
                                    ?->fonctions_visees
                            )
                            || filled(
                                $record
                                    ?->objectif_formation
                            )
                    )
                    ->schema([
                        TextEntry::make(
                            'fonctions_visees'
                        )
                            ->label(
                                'Fonction(s) visée(s)'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->fonctions_visees
                                    )
                            )
                            ->columnSpanFull(),

                        TextEntry::make(
                            'objectif_formation'
                        )
                            ->label(
                                'Objectif de la formation'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->objectif_formation
                                    )
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Modules et compétences — nouvelle génération'
                )
                    ->description(
                        'Modules développés et objectifs / compétences associés.'
                    )
                    ->visible(
                        fn ($record): bool =>
                            $record?->fifModules()
                                ->exists()
                    )
                    ->schema([
                        RepeatableEntry::make(
                            'fifModules'
                        )
                            ->label(
                                'Modules de la FIF'
                            )
                            ->schema([
                                TextEntry::make(
                                    'ordre'
                                )
                                    ->label('Ordre')
                                    ->badge(),

                                TextEntry::make(
                                    'module'
                                )
                                    ->label('Module')
                                    ->weight('bold')
                                    ->placeholder('—'),

                                TextEntry::make(
                                    'objectifs_competences'
                                )
                                    ->label(
                                        'Objectifs / compétences'
                                    )
                                    ->columnSpanFull()
                                    ->placeholder('—'),

                                TextEntry::make(
                                    'source'
                                )
                                    ->label('Source')
                                    ->badge()
                                    ->placeholder('—'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Compétences / certification — ancienne génération'
                )
                    ->description(
                        'Champs spécifiques aux anciennes FIF.'
                    )
                    ->visible(
                        fn ($record): bool =>
                            filled(
                                $record
                                    ?->domaines_competences_vises
                            )
                            || filled(
                                $record
                                    ?->criteres_certification
                            )
                    )
                    ->schema([
                        TextEntry::make(
                            'domaines_competences_vises'
                        )
                            ->label(
                                'Domaines de compétences visés'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->domaines_competences_vises
                                    )
                            )
                            ->columnSpanFull(),

                        TextEntry::make(
                            'criteres_certification'
                        )
                            ->label(
                                'Critères de certification'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->criteres_certification
                                    )
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Évaluations'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasEvaluationData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->schema([
                        TextEntry::make(
                            'evaluation_diagnostique'
                        )
                            ->label(
                                'Évaluation diagnostique'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->evaluation_diagnostique
                                    )
                            ),

                        TextEntry::make(
                            'evaluation_formative'
                        )
                            ->label(
                                'Évaluation formative'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->evaluation_formative
                                    )
                            ),

                        TextEntry::make(
                            'evaluation_certificative'
                        )
                            ->label(
                                'Évaluation certificative'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->evaluation_certificative
                                    )
                            ),

                        TextEntry::make(
                            'evaluation_format'
                        )
                            ->label(
                                'Format / modalités d’évaluation'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->evaluation_format
                                    )
                            ),
                    ]),

                Section::make(
                    'Pédagogie'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasPedagogyData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make(
                            'pedagogie_groupes'
                        )
                            ->label(
                                'Travaux / organisation en groupes'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->pedagogie_groupes
                                    )
                            ),

                        TextEntry::make(
                            'pedagogie_visite'
                        )
                            ->label(
                                'Visite / mise en situation'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->pedagogie_visite
                                    )
                            ),

                        TextEntry::make(
                            'pedagogie_video'
                        )
                            ->label(
                                'Vidéo / supports audiovisuels'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->pedagogie_video
                                    )
                            ),

                        TextEntry::make(
                            'pedagogie_tableau_interactif'
                        )
                            ->label(
                                'Tableau interactif / outils numériques'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->pedagogie_tableau_interactif
                                    )
                            ),

                        TextEntry::make(
                            'pedagogie_autre'
                        )
                            ->label(
                                'Autres modalités pédagogiques'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->pedagogie_autre
                                    )
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Formation à distance'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasEadData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        IconEntry::make(
                            'possibilite_ead'
                        )
                            ->label(
                                'Possibilité EAD'
                            )
                            ->boolean(),

                        TextEntry::make(
                            'duree_ead_ui'
                        )
                            ->label(
                                'Durée EAD / UI'
                            )
                            ->placeholder('—'),
                    ]),

                Section::make(
                    'Qualifications et cursus'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasQualificationData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make(
                            'diplomes_qualifications'
                        )
                            ->label(
                                'Diplômes / qualifications'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->diplomes_qualifications
                                    )
                            )
                            ->columnSpanFull(),

                        TextEntry::make(
                            'unite_certification'
                        )
                            ->label(
                                'Unité de certification'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->unite_certification
                                    )
                            ),

                        TextEntry::make(
                            'cursus_ouvert'
                        )
                            ->label('Cursus ouvert')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->cursus_ouvert
                                    )
                            ),

                        TextEntry::make(
                            'sirh'
                        )
                            ->label('SIRH')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->sirh
                                    )
                            ),

                        TextEntry::make(
                            'ouverture_licence'
                        )
                            ->label(
                                'Ouverture licence'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->ouverture_licence
                                    )
                            ),
                    ]),

                Section::make(
                    'CDF et informations catalogue'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasCatalogueData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make(
                            'date_cdf'
                        )
                            ->label('Date CDF')
                            ->date('d/m/Y')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->date_cdf
                                    )
                            ),

                        TextEntry::make(
                            'ecole_pilote_cdf'
                        )
                            ->label(
                                'École pilote CDF'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->ecole_pilote_cdf
                                    )
                            ),

                        TextEntry::make(
                            'date_creation_catalogue'
                        )
                            ->label(
                                'Création catalogue'
                            )
                            ->date('d/m/Y')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->date_creation_catalogue
                                    )
                            ),

                        TextEntry::make(
                            'date_maj_catalogue'
                        )
                            ->label(
                                'Dernière mise à jour'
                            )
                            ->date('d/m/Y')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->date_maj_catalogue
                                    )
                            ),

                        TextEntry::make(
                            'nature_maj'
                        )
                            ->label(
                                'Nature de la mise à jour'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->nature_maj
                                    )
                            )
                            ->columnSpanFull(),

                        TextEntry::make(
                            'observations'
                        )
                            ->label('Observations')
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->observations
                                    )
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make(
                    'Traçabilité FIF'
                )
                    ->visible(
                        fn ($record): bool =>
                            self::hasTraceabilityData(
                                $record
                            )
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make(
                            'fif_source_fichier'
                        )
                            ->label(
                                'Fichier FIF source'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->fif_source_fichier
                                    )
                            ),

                        TextEntry::make(
                            'fif_imported_at'
                        )
                            ->label(
                                'Dernier import FIF'
                            )
                            ->dateTime(
                                'd/m/Y H:i'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->fif_imported_at
                                    )
                            ),

                        TextEntry::make(
                            'fif_import_hash'
                        )
                            ->label(
                                'Empreinte de l’import'
                            )
                            ->visible(
                                fn ($record): bool =>
                                    filled(
                                        $record
                                            ?->fif_import_hash
                                    )
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function hasLegacyPopulationData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            (bool) $record->ouvert_off
            || (bool) $record->ouvert_om
            || (bool) $record->ouvert_qmm_mo
            || (bool) $record->ouverture_etrangers
            || (bool) $record->ouverture_vca
            || (bool) $record->ouverture_vae
            || filled(
                $record->autres_beneficiaires
            );
    }

    private static function hasEvaluationData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            filled(
                $record->evaluation_diagnostique
            )
            || filled(
                $record->evaluation_formative
            )
            || filled(
                $record->evaluation_certificative
            )
            || filled(
                $record->evaluation_format
            );
    }

    private static function hasPedagogyData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            filled(
                $record->pedagogie_groupes
            )
            || filled(
                $record->pedagogie_visite
            )
            || filled(
                $record->pedagogie_video
            )
            || filled(
                $record->pedagogie_tableau_interactif
            )
            || filled(
                $record->pedagogie_autre
            );
    }

    private static function hasEadData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            (bool) $record->possibilite_ead
            || filled(
                $record->duree_ead_ui
            );
    }

    private static function hasQualificationData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            filled(
                $record->diplomes_qualifications
            )
            || filled(
                $record->unite_certification
            )
            || filled(
                $record->cursus_ouvert
            )
            || filled(
                $record->sirh
            )
            || filled(
                $record->ouverture_licence
            );
    }

    private static function hasCatalogueData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            filled(
                $record->date_cdf
            )
            || filled(
                $record->ecole_pilote_cdf
            )
            || filled(
                $record->date_creation_catalogue
            )
            || filled(
                $record->date_maj_catalogue
            )
            || filled(
                $record->nature_maj
            )
            || filled(
                $record->observations
            );
    }

    private static function hasTraceabilityData(
        $record
    ): bool {
        if (! $record) {
            return false;
        }

        return
            filled(
                $record->fif_source_fichier
            )
            || filled(
                $record->fif_imported_at
            )
            || filled(
                $record->fif_import_hash
            );
    }
}
