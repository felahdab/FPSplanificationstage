<?php

namespace Modules\PlanificationStages\Filament\Resources\Salles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PlanificationStages\Filament\Resources\Salles\SalleResource;

class ViewSalle extends ViewRecord
{
    protected static string $resource = SalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
