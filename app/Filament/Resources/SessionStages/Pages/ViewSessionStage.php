<?php

namespace Modules\PlanificationStages\Filament\Resources\SessionStages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PlanificationStages\Filament\Resources\SessionStages\SessionStageResource;

class ViewSessionStage extends ViewRecord
{
    protected static string $resource = SessionStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
