<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InstructeurInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('matricule')
                    ->label('Matricule')
                    ->placeholder('-'),
                TextEntry::make('nid')
                    ->label('NID')
                    ->placeholder('-'),
                TextEntry::make('nom'),
                TextEntry::make('prenom'),
                TextEntry::make('email')
                    ->label('Adresse e-mail')
                    ->placeholder('-'),
                TextEntry::make('grade.libelle_court')
                    ->label('Grade')
                    ->placeholder('-'),
                TextEntry::make('specialite.libelle_court')
                    ->label('Spécialité')
                    ->placeholder('-'),
                TextEntry::make('unite.libelle_court')
                    ->label('Unité')
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
