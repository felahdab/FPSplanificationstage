<?php

use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;
use Modules\FPSplanificationstage\Filament\Public\Pages\BesoinConfirmation;
use Modules\FPSplanificationstage\Filament\Public\Pages\BesoinNouveau;
use Modules\FPSplanificationstage\Filament\Public\Pages\BesoinSuivi;
use Modules\FPSplanificationstage\Filament\Public\Pages\BesoinSuiviRecherche;
use Modules\FPSplanificationstage\Filament\Public\Pages\Inscription;
use Modules\FPSplanificationstage\Filament\Public\Pages\InscriptionConfirmation;
use Modules\FPSplanificationstage\Filament\Public\Pages\SessionDetail;

it('all migrated screens remain filament pages', function (): void {
    foreach ([
        SessionDetail::class,
        Inscription::class,
        InscriptionConfirmation::class,
        BesoinNouveau::class,
        BesoinSuiviRecherche::class,
        BesoinConfirmation::class,
        BesoinSuivi::class,
    ] as $pageClass) {
        expect(is_subclass_of($pageClass, Page::class))
            ->toBeTrue();
    }
});

it('keeps action routes below the main planning path', function (): void {
    foreach ([
        'fpsplanificationstage.public.inscription.store'
            => [
                'apps/fpsplanificationstage/espace-stagiaire/planning-formations/sessions/{session}/inscription',
                'POST',
            ],

        'fpsplanificationstage.public.besoin.store'
            => [
                'apps/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/nouveau',
                'POST',
            ],

        'fpsplanificationstage.public.besoin.suivi.rechercher'
            => [
                'apps/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/suivi',
                'POST',
            ],

        'fpsplanificationstage.public.inscription.pdf'
            => [
                'apps/fpsplanificationstage/espace-stagiaire/planning-formations/inscriptions/{code}/pdf',
                'GET',
            ],
    ] as $name => [$expectedUri, $method]) {
        $route = Route::getRoutes()->getByName($name);

        expect($route)->not->toBeNull();
        expect($route->uri())->toBe($expectedUri);
        expect($route->methods())->toContain($method);
    }
});
