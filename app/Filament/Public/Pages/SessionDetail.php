<?php

namespace Modules\FPSplanificationstage\Filament\Public\Pages;

use Modules\FPSplanificationstage\Http\Controllers\PublicPlanningController;
use Modules\FPSplanificationstage\Models\SessionStage;

class SessionDetail extends PublicPage
{
    protected string $view = 'fpsplanificationstage::filament.public.formation-detail';

    protected static ?string $slug = 'espace-stagiaire/planning-formations/sessions/{session}';

    public function mount(int|string $session): void
    {
        $record = SessionStage::query()->findOrFail($session);

        $view = app(PublicPlanningController::class)->show($record);

        $this->pageData = $view->getData();
    }
}
