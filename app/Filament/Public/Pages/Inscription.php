<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicInscriptionController;
use Modules\FPSplanificationstage\Models\SessionStage;

class Inscription extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.inscription';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/sessions/{session}/inscription';

    public function mount(int|string $session): void
    {
        $record = SessionStage::query()->findOrFail($session);

        $view = app(PublicInscriptionController::class)->create($record);

        $this->pageData = $view->getData();
    }
}
