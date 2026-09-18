<?php

namespace Modules\PlanificationStages\Filament\Resources\Stagiaires;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\PlanificationStages\Filament\Resources\Stagiaires\Pages\ListStagiaires;
use Modules\PlanificationStages\Filament\Resources\Stagiaires\Pages\ViewStagiaire;
use Modules\PlanificationStages\Filament\Resources\Stagiaires\Tables\StagiairesTable;
use Modules\PlanificationStages\Models\Stagiaire;

class StagiaireResource extends Resource
{
    protected static ?string $model =
        Stagiaire::class;

    protected static ?string $navigationLabel =
        'Historique stagiaires';

    protected static string|\UnitEnum|null $navigationGroup =
        'Inscriptions';

    protected static ?int $navigationSort = 90;

    public static function form(
        Schema $schema
    ): Schema {
        return $schema;
    }

    public static function table(
        Table $table
    ): Table {
        return StagiairesTable::configure(
            $table
        );
    }

    public static function getModelLabel(): string
    {
        return 'stagiaire';
    }

    public static function getPluralModelLabel(): string
    {
        return 'stagiaires';
    }

    public static function getPages(): array
    {
        return [
            'index' =>
                ListStagiaires::route('/'),

            'view' =>
                ViewStagiaire::route(
                    '/{record}'
                ),
        ];
    }
}
