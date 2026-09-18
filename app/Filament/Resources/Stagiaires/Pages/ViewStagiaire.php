<?php

namespace Modules\PlanificationStages\Filament\Resources\Stagiaires\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\PlanificationStages\Filament\Resources\Stagiaires\StagiaireResource;

class ViewStagiaire extends ViewRecord
{
    protected static string $resource =
        StagiaireResource::class;

    protected string $view =
        'planificationstages::filament.resources.stagiaires.pages.view-stagiaire';

    public function getTitle(): string
    {
        return $this
            ->getRecord()
            ->nom_complet;
    }
}
