<?php

namespace Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PlanificationStages\Filament\Resources\IndisponibiliteInstructeurs\IndisponibiliteInstructeurResource;

class ViewIndisponibiliteInstructeur extends ViewRecord
{
    protected static string $resource = IndisponibiliteInstructeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
