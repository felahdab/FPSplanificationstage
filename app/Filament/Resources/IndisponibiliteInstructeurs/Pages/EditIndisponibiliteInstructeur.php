<?php

namespace Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\IndisponibiliteInstructeurResource;

class EditIndisponibiliteInstructeur extends EditRecord
{
    protected static string $resource = IndisponibiliteInstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
