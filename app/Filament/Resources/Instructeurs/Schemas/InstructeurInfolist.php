<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InstructeurInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('identifiant_interne')
                    ->placeholder('-'),
                TextEntry::make('nom'),
                TextEntry::make('prenom'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                IconEntry::make('actif')
                    ->boolean(),
                TextEntry::make('salle_preferentielle')
                    ->placeholder('-'),
                TextEntry::make('commentaire')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('import_match_key')
                    ->placeholder('-'),
                TextEntry::make('import_hash')
                    ->placeholder('-'),
                TextEntry::make('dernier_import_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
