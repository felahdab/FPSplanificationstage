<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\FPSplanificationstage\Models\Salle;
use Modules\FPSplanificationstage\Models\Stage;

class InstructeurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Identité de l’instructeur')
                    ->description(
                        'Informations permettant d’identifier l’instructeur.'
                    )
                    ->columns(2)
                    ->schema([

                        TextInput::make('identifiant_interne')
                            ->label('Identifiant interne')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Toggle::make('actif')
                            ->label('Instructeur actif')
                            ->default(true),

                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Adresse e-mail')
                            ->email()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Stages délivrés')
                    ->description(
                        'Sélectionnez le ou les stages que cet instructeur est habilité à délivrer.'
                    )
                    ->schema([

                        Select::make('stages')
                            ->label('Stages')
                            ->relationship(
                                name: 'stages',
                                titleAttribute: 'libelle_court'
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(
                                fn (Stage $record): string =>
                                    ($record->code_stage
                                        ? $record->code_stage . ' — '
                                        : '')
                                    . $record->libelle_court
                            )
                            ->helperText(
                                'Vous pouvez sélectionner plusieurs stages.'
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make('Préférences')
                    ->columns(2)
                    ->schema([

                        Select::make('salle_preferentielle_id')
    ->label('Salle préférentielle')
    ->relationship(
        name: 'sallePreferentielle',
        titleAttribute: 'nom',
        modifyQueryUsing: fn ($query) => $query->where('actif', true)
    )
    ->getOptionLabelFromRecordUsing(
        fn (Salle $record): string =>
            ($record->code
                ? $record->code . ' — '
                : '')
            . $record->nom
            . ($record->capacite
                ? ' (' . $record->capacite . ' pers.)'
                : '')
    )
    ->searchable()
    ->preload()
    ->placeholder('Aucune préférence')
    ->helperText(
        'Utilisée uniquement si le stage ne possède pas de salle préférentielle.'
    ),

                        Textarea::make('commentaire')
                            ->label('Commentaire')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}