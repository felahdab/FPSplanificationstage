<?php

namespace Modules\PlanificationStages\Filament\Resources\Stagiaires\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\PlanificationStages\Filament\Resources\Stagiaires\StagiaireResource;

class ListStagiaires extends ListRecords
{
    protected static string $resource =
        StagiaireResource::class;

    public function getTitle(): string
    {
        return 'Historique stagiaires';
    }
}
