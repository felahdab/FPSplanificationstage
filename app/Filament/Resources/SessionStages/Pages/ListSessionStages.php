<?php

namespace Modules\FPSplanificationstage\Filament\Resources\SessionStages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\FPSplanificationstage\Filament\Resources\SessionStages\SessionStageResource;

class ListSessionStages extends ListRecords
{
    protected static string $resource = SessionStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
