<?php

namespace Modules\PlanificationStages\Filament\Resources\Instructeurs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\PlanificationStages\Filament\Resources\Instructeurs\InstructeurResource;

class EditInstructeur extends EditRecord
{
    protected static string $resource = InstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
