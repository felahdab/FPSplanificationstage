<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Illuminate\View\View;
use Modules\FPSplanificationstage\Http\Controllers\PublicInscriptionController;

class InscriptionConfirmation extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.inscription-confirmation';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/inscriptions/{code}/confirmation';

    public function mount(string $code): void
    {
        $view = app(PublicInscriptionController::class)->confirmation(
            request(),
            $code
        );

        abort_unless($view instanceof View, 404);

        $this->pageData = $view->getData();
    }
}
