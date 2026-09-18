<?php

namespace Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages\CreateIndisponibiliteInstructeur;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages\EditIndisponibiliteInstructeur;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages\ListIndisponibiliteInstructeurs;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages\ViewIndisponibiliteInstructeur;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Schemas\IndisponibiliteInstructeurForm;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Schemas\IndisponibiliteInstructeurInfolist;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Tables\IndisponibiliteInstructeursTable;
use Modules\PlanificationStages\Models\IndisponibiliteInstructeur;

class IndisponibiliteInstructeurResource extends Resource
{
    protected static ?string $model =
        IndisponibiliteInstructeur::class;

    protected static ?string $navigationLabel =
        'Indisponibilités';

    protected static ?string $modelLabel =
        'indisponibilité';

    protected static ?string $pluralModelLabel =
        'indisponibilités';

    protected static string|\UnitEnum|null $navigationGroup =
        'Disponibilités';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return IndisponibiliteInstructeurForm::configure(
            $schema
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return IndisponibiliteInstructeurInfolist::configure(
            $schema
        );
    }

    public static function table(Table $table): Table
    {
        return IndisponibiliteInstructeursTable::configure(
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
                ListIndisponibiliteInstructeurs::route('/'),

            'create' =>
                CreateIndisponibiliteInstructeur::route('/create'),

            'view' =>
                ViewIndisponibiliteInstructeur::route('/{record}'),

            'edit' =>
                EditIndisponibiliteInstructeur::route(
                    '/{record}/edit'
                ),
        ];
    }
}