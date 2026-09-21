<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicBesoinFormationController;

class BesoinNouveau extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.besoin-formation';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/besoins/nouveau';

    public function mount(): void
    {
        $view = app(PublicBesoinFormationController::class)->create();

        $this->pageData = $view->getData();
    }
}
