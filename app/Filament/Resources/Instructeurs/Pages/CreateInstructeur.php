<?php

namespace Modules\PlanificationStages\Filament\Resources\Instructeurs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PlanificationStages\Filament\Resources\Instructeurs\InstructeurResource;

class CreateInstructeur extends CreateRecord
{
    protected static string $resource = InstructeurResource::class;
}
