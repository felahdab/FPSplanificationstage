<?php

namespace Modules\FPSplanificationstage\Filament\Resources\BesoinFormations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\FPSplanificationstage\Filament\Resources\BesoinFormations\BesoinFormationResource;

class ListBesoinFormations extends ListRecords
{
    protected static string $resource = BesoinFormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
