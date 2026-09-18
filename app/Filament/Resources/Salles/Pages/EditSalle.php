<?php

namespace Modules\PlanificationStages\Filament\Resources\Salles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\PlanificationStages\Filament\Resources\Salles\SalleResource;

class EditSalle extends EditRecord
{
    protected static string $resource = SalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
