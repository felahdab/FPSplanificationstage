<?php

namespace Modules\PlanificationStages\Filament\Resources\Stages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\PlanificationStages\Models\Salle;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification du stage')
                    ->description(
                        'Références internes, catalogue et informations générales du stage.'
                    )
                    ->columns(2)
                    ->schema([
                        TextInput::make('code_stage')
                            ->label('Code interne')
                            ->disabled()
                            ->placeholder('Généré automatiquement'),

                        TextInput::make('numero_externe')
                            ->label('Numéro catalogue'),

                        TextInput::make('libelle_court')
                            ->label('Libellé court')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('libelle_long')
                            ->label('Libellé long')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('appellation_chorus')
                            ->label('Appellation CHORUS'),

                        TextInput::make('centre_formation')
                            ->label('Centre de formation'),

                        TextInput::make('typologie')
                            ->label('Typologie'),

                        TextInput::make('branche')
                            ->label('Branche'),

                        TextInput::make('raf')
                            ->label('RAF'),

                        TextInput::make('adc')
                            ->label('ADC'),
                    ]),

                Section::make('FIF — Identification')
                    ->description(
                        'Données issues d’une Fiche d’Information Formation, ancienne ou nouvelle génération.'
                    )
                    ->columns(2)
                    ->schema([
                        Select::make('fif_generation')
                            ->label('Génération FIF')
                            ->options([
                                'nouvelle' => 'Nouvelle génération',
                                'ancienne' => 'Ancienne génération',
                            ])
                            ->placeholder('Non renseignée'),

                        TextInput::make('service_emetteur')
                            ->label('Service émetteur'),

                        Textarea::make('intitule_formation')
                            ->label('Intitulé de la formation')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('si_enregistrement_qualification')
                            ->label('SI d’enregistrement / qualification')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('echelle_grades')
                            ->label('Échelle de grades / population')
                            ->rows(2),

                        Textarea::make('niveau_brevet')
                            ->label('Niveau / brevet')
                            ->rows(2),

                        Textarea::make('lieux_formation')
                            ->label('Lieu(x) de formation')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Organisation du stage')
                    ->description(
                        'La durée accepte les demi-journées et n’est plus limitée à 5 jours.'
                    )
                    ->columns(3)
                    ->schema([
                        TextInput::make('duree_jours')
                            ->label('Durée en jours')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0.5)
                            ->placeholder('Ex. 0,5 ; 2 ; 7,5'),

                        TextInput::make('nb_sessions_annuelles')
                            ->label('Nombre de sessions / an')
                            ->numeric()
                            ->minValue(0),

                        Toggle::make('actif')
                            ->label('Stage actif')
                            ->default(true),

                        TextInput::make('capacite_min')
                            ->label('Capacité minimale')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('capacite_max')
                            ->label('Capacité maximale')
                            ->numeric()
                            ->minValue(1),

                        Select::make('salle_preferentielle_id')
                            ->label('Salle préférentielle')
                            ->relationship(
                                name: 'sallePreferentielle',
                                titleAttribute: 'nom'
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Salle $record): string =>
                                    ($record->code ? $record->code . ' — ' : '')
                                    . $record->nom
                                    . ($record->capacite
                                        ? ' (' . $record->capacite . ' pers.)'
                                        : '')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Aucune salle préférentielle')
                            ->helperText(
                                'Cette salle sera prioritaire lors de la planification du stage.'
                            ),
                    ]),

                Section::make('Population concernée')
                    ->columns(3)
                    ->schema([
                        Toggle::make('ouvert_off')
                            ->label('OFF'),

                        Toggle::make('ouvert_om')
                            ->label('OM'),

                        Toggle::make('ouvert_qmm_mo')
                            ->label('QMM / MO'),

                        Toggle::make('ouverture_etrangers')
                            ->label('Ouvert aux étrangers'),

                        Toggle::make('ouverture_vca')
                            ->label('Ouverture VCA'),

                        Toggle::make('ouverture_vae')
                            ->label('Ouverture VAE'),

                        Textarea::make('autres_beneficiaires')
                            ->label('Autres bénéficiaires')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Prérequis')
                    ->description(
                        'Conditions que le candidat devra déclarer lors de son inscription. Les prérequis importés par FIF restent modifiables.'
                    )
                    ->schema([
                        Repeater::make('prerequis')
                            ->label('Liste des prérequis')
                            ->relationship()
                            ->orderColumn('ordre')
                            ->schema([
                                Textarea::make('libelle')
                                    ->label('Prérequis')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Toggle::make('obligatoire')
                                    ->label('Obligatoire')
                                    ->default(true),

                                Toggle::make('actif')
                                    ->label('Actif')
                                    ->default(true),

                                Hidden::make('source')
                                    ->default('manuel'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un prérequis')
                            ->itemLabel(
                                fn (array $state): ?string =>
                                    $state['libelle'] ?? 'Nouveau prérequis'
                            )
                            ->collapsible()
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Finalités de la formation')
                    ->description(
                        'Fonctions visées et objectif général de la formation.'
                    )
                    ->schema([
                        Textarea::make('fonctions_visees')
                            ->label('Fonction(s) visée(s)')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('objectif_formation')
                            ->label('Objectif de la formation')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('Modules et compétences — nouvelle génération')
                    ->description(
                        'Modules développés et objectifs / compétences associés dans les nouvelles FIF.'
                    )
                    ->schema([
                        Repeater::make('fifModules')
                            ->label('Modules de la FIF')
                            ->relationship()
                            ->orderColumn('ordre')
                            ->schema([
                                TextInput::make('module')
                                    ->label('Module')
                                    ->columnSpanFull(),

                                Textarea::make('objectifs_competences')
                                    ->label('Objectifs / compétences')
                                    ->rows(4)
                                    ->columnSpanFull(),

                                Hidden::make('source')
                                    ->default('manuel'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un module')
                            ->itemLabel(
                                fn (array $state): ?string =>
                                    $state['module'] ?? 'Nouveau module'
                            )
                            ->collapsible()
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Compétences / certification — ancienne génération')
                    ->description(
                        'Champs spécifiques aux anciennes FIF, conservés séparément des modules de la nouvelle génération.'
                    )
                    ->collapsed()
                    ->schema([
                        Textarea::make('domaines_competences_vises')
                            ->label('Domaines de compétences visés')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('criteres_certification')
                            ->label('Critères de certification')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('Évaluations')
                    ->columns(2)
                    ->schema([
                        Textarea::make('evaluation_diagnostique')
                            ->label('Évaluation diagnostique')
                            ->rows(3),

                        Textarea::make('evaluation_formative')
                            ->label('Évaluation formative')
                            ->rows(3),

                        Textarea::make('evaluation_certificative')
                            ->label('Évaluation certificative')
                            ->rows(3),

                        Textarea::make('evaluation_format')
                            ->label('Format / modalités d’évaluation')
                            ->rows(3),
                    ]),

                Section::make('Pédagogie')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Textarea::make('pedagogie_groupes')
                            ->label('Travaux / organisation en groupes')
                            ->rows(2),

                        Textarea::make('pedagogie_visite')
                            ->label('Visite / mise en situation')
                            ->rows(2),

                        Textarea::make('pedagogie_video')
                            ->label('Vidéo / supports audiovisuels')
                            ->rows(2),

                        Textarea::make('pedagogie_tableau_interactif')
                            ->label('Tableau interactif / outils numériques')
                            ->rows(2),

                        Textarea::make('pedagogie_autre')
                            ->label('Autres modalités pédagogiques')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Formation à distance')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Toggle::make('possibilite_ead')
                            ->label('Possibilité EAD'),

                        TextInput::make('duree_ead_ui')
                            ->label('Durée EAD / UI')
                            ->numeric()
                            ->step(0.5),
                    ]),

                Section::make('Qualifications et cursus')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Textarea::make('diplomes_qualifications')
                            ->label('Diplômes / qualifications')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('unite_certification')
                            ->label('Unité de certification'),

                        TextInput::make('cursus_ouvert')
                            ->label('Cursus ouvert'),

                        TextInput::make('sirh')
                            ->label('SIRH'),

                        TextInput::make('ouverture_licence')
                            ->label('Ouverture licence'),
                    ]),

                Section::make('CDF et informations catalogue')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        DatePicker::make('date_cdf')
                            ->label('Date CDF'),

                        TextInput::make('ecole_pilote_cdf')
                            ->label('École pilote CDF'),

                        DatePicker::make('date_creation_catalogue')
                            ->label('Date de création catalogue'),

                        DatePicker::make('date_maj_catalogue')
                            ->label('Date de mise à jour'),

                        Textarea::make('nature_maj')
                            ->label('Nature de la mise à jour')
                            ->columnSpanFull(),

                        Textarea::make('observations')
                            ->label('Observations')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Traçabilité FIF')
                    ->description(
                        'Informations techniques de l’import. Elles sont affichées en lecture seule.'
                    )
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('fif_source_fichier')
                            ->label('Fichier FIF source')
                            ->disabled(),

                        DateTimePicker::make('fif_imported_at')
                            ->label('Dernier import FIF')
                            ->disabled()
                            ->seconds(false),

                        TextInput::make('fif_import_hash')
                            ->label('Empreinte de l’import')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
