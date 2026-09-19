<?php

namespace Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire;

use Filament\Pages\Page;
use Modules\FPSplanificationstage\Http\Controllers\PublicPlanningController;

class PlanningFormations extends Page
{
    protected string $view =
        'fpsplanificationstage::filament.pages.espace-stagiaire.planning-formations';

    protected static ?string $navigationLabel =
        'Planning des formations';

    protected static string|\UnitEnum|null $navigationGroup =
        'Espace stagiaire';

    protected static ?int $navigationSort =
        10000;

    protected static ?string $slug =
        'espace-stagiaire/planning-formations';

    public function getTitle(): string
    {
        return 'Planning des formations';
    }

    /**
     * Étape de migration :
     * la page Filament réutilise temporairement la logique
     * métier déjà validée du contrôleur public.
     *
     * Quand les quatre pages seront migrées, cette logique
     * pourra être déplacée dans un service dédié.
     */
    public function getViewData(): array
    {
        $view =
            app(
                PublicPlanningController::class
            )->index(
                request()
            );

        return $view->getData();
    }
}
