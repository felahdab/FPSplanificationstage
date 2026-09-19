<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Instructeurs;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Pages\CreateInstructeur;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Pages\EditInstructeur;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Pages\ListInstructeurs;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Pages\ViewInstructeur;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Schemas\InstructeurForm;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Schemas\InstructeurInfolist;
use Modules\FPSplanificationstage\Filament\Resources\Instructeurs\Tables\InstructeursTable;
use Modules\FPSplanificationstage\Models\Instructeur;

class InstructeurResource extends Resource
{
    protected static ?string $model =
        Instructeur::class;

    protected static ?string $navigationLabel =
        'Instructeurs';

    protected static ?string $modelLabel =
        'instructeur';

    protected static ?string $pluralModelLabel =
        'instructeurs';

    protected static string|\UnitEnum|null $navigationGroup =
        'Référentiels';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return InstructeurForm::configure(
            $schema
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return InstructeurInfolist::configure(
            $schema
        );
    }

    public static function table(Table $table): Table
    {
        return InstructeursTable::configure(
            $table
        );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' =>
                ListInstructeurs::route('/'),

            'create' =>
                CreateInstructeur::route('/create'),

            'view' =>
                ViewInstructeur::route('/{record}'),

            'edit' =>
                EditInstructeur::route('/{record}/edit'),
        ];
    }
}