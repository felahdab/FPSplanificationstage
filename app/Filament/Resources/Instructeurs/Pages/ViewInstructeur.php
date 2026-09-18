<?php

namespace Modules\PlanificationStages\Filament\Resources\Instructeurs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PlanificationStages\Filament\Resources\Instructeurs\InstructeurResource;

class ViewInstructeur extends ViewRecord
{
    protected static string $resource = InstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
