<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire\PlanningFormations;

uses(Tests\TestCase::class);
uses()->group('FPSplanificationstage');

it('page metadata matches espace stagiaire', function () {
    expect(PlanningFormations::getNavigationLabel())->toBe('Planning des formations')
        ->and(PlanningFormations::getNavigationGroup())->toBe('Espace stagiaire');
});

it('page is registered in fpsplanificationstage panel', function () {
    $panel = Filament::getPanel('fpsplanificationstage');

    expect($panel->getPages())->toContain(PlanningFormations::class);
});

it('page view exists and is a filament page', function () {
    $path = base_path('Modules/FPSplanificationstage/resources/views/filament/pages/espace-stagiaire/planning-formations.blade.php');

    $this->assertFileExists($path);

    $source = file_get_contents($path);
    $this->assertIsString($source);

    $this->assertStringContainsString('ESPACE_STAGIAIRE_PLANNING_FILAMENT_V1', $source);
    $this->assertStringContainsString('<x-filament-panels::page>', $source);
    $this->assertStringContainsString('PORTAIL_VUE_SEMAINE_V1', $source);
    $this->assertStringContainsString('ps-stagiaire-planning', $source);
});

it('planning page reloads calendar when filters change', function () {
    $path = base_path('Modules/FPSplanificationstage/resources/views/filament/pages/planning.blade.php');
    $source = file_get_contents($path);

    $this->assertIsString($source);
    $this->assertStringContainsString('wire:model.live="stageFilter"', $source);
    $this->assertStringContainsString('key($this->filterKey())', $source);
});

it('page class reuses existing planning logic', function () {
    $path = base_path('Modules/FPSplanificationstage/app/Filament/Pages/EspaceStagiaire/PlanningFormations.php');
    $source = file_get_contents($path);

    $this->assertIsString($source);
    $this->assertStringContainsString('PublicPlanningController::class', $source);
    $this->assertStringContainsString('->getData()', $source);
});

it('generated blade source compiles', function () {
    $path = base_path('Modules/FPSplanificationstage/resources/views/filament/pages/espace-stagiaire/planning-formations.blade.php');
    $source = file_get_contents($path);

    $this->assertIsString($source);

    $compiled = Blade::compileString($source);
    $this->assertNotSame('', trim($compiled));
});
