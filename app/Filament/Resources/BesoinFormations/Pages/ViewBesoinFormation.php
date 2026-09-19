<?php

namespace Modules\FPSplanificationstage\Filament\Resources\BesoinFormations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\FPSplanificationstage\Filament\Resources\BesoinFormations\BesoinFormationResource;

class ViewBesoinFormation extends ViewRecord
{
    protected static string $resource = BesoinFormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
