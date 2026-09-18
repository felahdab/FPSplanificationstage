<?php

namespace Modules\PlanificationStages\Filament\Resources\Salles;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\PlanificationStages\Filament\Resources\Salles\Pages\CreateSalle;
use Modules\PlanificationStages\Filament\Resources\Salles\Pages\EditSalle;
use Modules\PlanificationStages\Filament\Resources\Salles\Pages\ListSalles;
use Modules\PlanificationStages\Filament\Resources\Salles\Pages\ViewSalle;
use Modules\PlanificationStages\Filament\Resources\Salles\Schemas\SalleForm;
use Modules\PlanificationStages\Filament\Resources\Salles\Schemas\SalleInfolist;
use Modules\PlanificationStages\Filament\Resources\Salles\Tables\SallesTable;
use Modules\PlanificationStages\Models\Salle;

class SalleResource extends Resource
{
    protected static ?string $model =
        Salle::class;

    protected static ?string $navigationLabel =
        'Salles';

    protected static ?string $modelLabel =
        'salle';

    protected static ?string $pluralModelLabel =
        'salles';

    protected static string|\UnitEnum|null $navigationGroup =
        'Référentiels';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return SalleForm::configure(
            $schema
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalleInfolist::configure(
            $schema
        );
    }

    public static function table(Table $table): Table
    {
        return SallesTable::configure(
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
                ListSalles::route('/'),

            'create' =>
                CreateSalle::route('/create'),

            'view' =>
                ViewSalle::route('/{record}'),

            'edit' =>
                EditSalle::route('/{record}/edit'),
        ];
    }
}