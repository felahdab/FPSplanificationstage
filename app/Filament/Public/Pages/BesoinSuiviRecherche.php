<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicBesoinFormationController;

class BesoinSuiviRecherche extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.besoin-formation-suivi-recherche';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/besoins/suivi';

    public function mount(): void
    {
        $view = app(PublicBesoinFormationController::class)->suiviForm();

        $this->pageData = $view->getData();
    }
}
