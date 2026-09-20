<?php

use Filament\Facades\Filament;
use Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire\PlanningFormations;
use Modules\FPSplanificationstage\Filament\Widgets\PlanningCalendar;

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
