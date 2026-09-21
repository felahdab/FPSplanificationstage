<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicBesoinFormationController;

class BesoinSuivi extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.besoin-formation-suivi';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/besoins/{token}/suivi';

    public function mount(string $token): void
    {
        $view = app(PublicBesoinFormationController::class)->suivi($token);

        $this->pageData = $view->getData();
    }
}
