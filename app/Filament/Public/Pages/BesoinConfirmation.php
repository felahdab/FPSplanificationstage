<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicBesoinFormationController;

class BesoinConfirmation extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.besoin-formation-confirmation';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/besoins/{token}/confirmation';

    public function mount(string $token): void
    {
        $view = app(PublicBesoinFormationController::class)->confirmation($token);

        $this->pageData = $view->getData();
    }
}
