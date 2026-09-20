<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\View;
use Modules\FPSplanificationstage\Filament\Widgets\PlanningCalendar;
use Modules\FPSplanificationstage\Http\Controllers\PublicInscriptionController;
use Modules\FPSplanificationstage\Http\Controllers\PublicPlanningController;
use Modules\FPSplanificationstage\Models\SessionStage;
use Modules\FPSplanificationstage\Models\Stage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);
uses()->group('FPSplanificationstage');

function createPublicNavigationFixture(): SessionStage
{
    $stage = Stage::create([
        'code_stage' => 'STG-PUBLIC-NAV',
        'libelle_court' => 'Formation navigation publique',
        'libelle_long' => 'Description complète de la formation.',
        'objectif_formation' => 'Objectif de la formation.',
        'actif' => true,
    ]);

    return SessionStage::create([
        'stage_id' => $stage->id,
        'debut' => '2026-10-12 08:00:00',
        'fin' => '2026-10-16 16:00:00',
        'capacite_max' => 12,
        'statut' => 'planifiee',
    ]);
}

it('public routes are registered on the expected controllers', function () {
    $routes = app('router')->getRoutes();

    expect(
        $routes
            ->getByName('fpsplanificationstage.public.session.show')
            ?->getActionName()
    )->toContain('PublicPlanningController@show');

    expect(
        $routes
            ->getByName('fpsplanificationstage.public.inscription.create')
            ?->getActionName()
    )->toContain('PublicInscriptionController@create');
});

it('detail controller renders description and registration link', function () {
    $session = createPublicNavigationFixture();

    $view = app(PublicPlanningController::class)
        ->show($session);

    expect($view)
        ->toBeInstanceOf(View::class)
        ->and($view->name())
        ->toBe('fpsplanificationstage::public.formation-detail');

    $html = $view->render();

    expect($html)
        ->toContain('Formation navigation publique')
        ->toContain('Description complète de la formation.')
        ->toContain('Objectif de la formation.')
        ->toContain("Je m'inscris à cette formation")
        ->toContain(
            route(
                'fpsplanificationstage.public.inscription.create',
                ['session' => $session->id],
                false
            )
        );
});

it('existing inscription controller still accepts the same session', function () {
    $session = createPublicNavigationFixture();

    $view = app(PublicInscriptionController::class)
        ->create($session);

    expect($view)
        ->toBeInstanceOf(View::class)
        ->and($view->name())
        ->toBe('fpsplanificationstage::public.inscription');
});

it('calendar links to public detail instead of admin edit', function () {
    $source = file_get_contents(
        (new ReflectionClass(PlanningCalendar::class))
            ->getFileName()
    );

    expect($source)
        ->toContain('fpsplanificationstage.public.session.show')
        ->not->toContain('SessionStageResource::getUrl');
});
