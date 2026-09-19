<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\FPSplanificationstage\Filament\Resources\Stages\StageResource;

class ViewStage extends ViewRecord
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
