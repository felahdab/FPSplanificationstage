<?php

namespace Modules\PlanificationStages\Filament\Resources\Salles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Identification')
                    ->columns(2)
                    ->schema([

                        TextInput::make('code')
                            ->label('Code de la salle')
                            ->placeholder('Ex. SALLE-01')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Toggle::make('actif')
                            ->label('Salle active')
                            ->default(true),

                        TextInput::make('nom')
                            ->label('Nom de la salle')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('capacite')
                            ->label('Capacité')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('personnes'),

                        TextInput::make('localisation')
                            ->label('Localisation')
                            ->placeholder('Ex. Bâtiment A - RDC')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Caractéristiques')
                    ->columns(2)
                    ->schema([

                        Select::make('type_salle')
                            ->label('Type de salle')
                            ->options([
                                'cours' => 'Salle de cours',
                                'informatique' => 'Salle informatique',
                                'simulateur' => 'Simulateur',
                                'conference' => 'Salle de conférence',
                                'atelier' => 'Atelier',
                                'autre' => 'Autre',
                            ])
                            ->searchable(),

                        Textarea::make('equipements')
                            ->label('Équipements')
                            ->placeholder(
                                'Vidéoprojecteur, postes informatiques, simulateur...'
                            )
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('commentaire')
                            ->label('Commentaire')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}