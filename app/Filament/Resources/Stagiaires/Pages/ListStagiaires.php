<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Stagiaires\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\FPSplanificationstage\Filament\Resources\Stagiaires\StagiaireResource;

class ListStagiaires extends ListRecords
{
    protected static string $resource =
        StagiaireResource::class;

    public function getTitle(): string
    {
        return 'Historique des formations';
    }
}
