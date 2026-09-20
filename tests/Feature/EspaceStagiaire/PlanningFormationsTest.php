<?php

use Filament\Facades\Filament;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire\PlanningFormations;
use Modules\FPSplanificationstage\Filament\Widgets\PlanningCalendar;
use Modules\FPSplanificationstage\Models\SessionStage;
use Modules\FPSplanificationstage\Models\Stage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);
uses()->group('FPSplanificationstage');

it('page metadata matches espace stagiaire', function () {
    expect(PlanningFormations::getNavigationLabel())->toBe('Planning des formations')
        ->and(PlanningFormations::getNavigationGroup())->toBe('Espace stagiaire');
});

it('page is registered in fpsplanificationstage panel', function () {
    $panel = Filament::getPanel('fpsplanificationstage');

    expect($panel->getPages())->toContain(PlanningFormations::class);
});

it('page uses native filament schema and guava calendar', function () {
    $page = new PlanningFormations();

    expect(method_exists($page, 'content'))->toBeTrue()
        ->and($page->content(resolve(\Filament\Schemas\Schema::class)))->toBeInstanceOf(\Filament\Schemas\Schema::class);

    $reflection = new ReflectionClass(PlanningFormations::class);
    $source = file_get_contents($reflection->getFileName());

    expect($source)
        ->toContain('Filament\\Schemas\\Schema')
        ->toContain('Livewire::make(PlanningCalendar::class')
        ->toContain('searchTerm');
});

it('calendar widget keeps live search filtering', function () {
    $reflection = new ReflectionClass(PlanningCalendar::class);
    $source = file_get_contents($reflection->getFileName());

    expect($source)
        ->toContain('public ?string $searchTerm =')
        ->toContain('this->searchTerm')
        ->toContain("whereHas('stage'");
});

it('shows every session when all statuses are selected', function () {
    app('url')->resolveMissingNamedRoutesUsing(
        fn (): string => '/testing/session-stages'
    );

    $stage = Stage::create([
        'code_stage' => 'STG-ALL-STATUSES',
        'libelle_court' => 'Tous les statuts',
        'actif' => true,
    ]);

    foreach (['brouillon', 'planifiee', 'confirmee', 'annulee', 'terminee'] as $status) {
        SessionStage::create([
            'stage_id' => $stage->id,
            'debut' => '2026-09-15 09:00:00',
            'fin' => '2026-09-15 17:00:00',
            'statut' => $status,
        ]);
    }

    $widget = new PlanningCalendar();
    $widget->statutFilter = '';

    $method = (new ReflectionClass($widget))->getMethod('getEvents');
    $events = $method->invoke(
        $widget,
        new FetchInfo([
            'startStr' => '2026-09-01T00:00:00',
            'endStr' => '2026-10-01T00:00:00',
        ])
    );

    expect($events)->toHaveCount(5);
});
