<?php

use Illuminate\Support\Collection;

uses(Tests\TestCase::class);
uses()->group('FPSplanificationstage');

function calendrierPageData(string $viewMode): array
{
    return [
        'days' => [],
        'moisLabel' => 'Septembre 2026',
        'moisPrecedent' => '2026-08',
        'moisSuivant' => '2026-10',
        'moisActuel' => '2026-09',
        'moisCourant' => '2026-09',

        'weekDays' => [],
        'weekLabel' => 'Semaine du 14 au 18 septembre 2026',
        'semainePrecedente' => '2026-09-07',
        'semaineSuivante' => '2026-09-21',
        'semaineActuelle' => '2026-09-14',
        'semaineCourante' => '2026-09-14',

        'viewMode' => $viewMode,
        'searchTerm' => '',
        'listSessions' => new Collection(),
        'matchingSessionsCount' => null,
    ];
}

it('calendar view renders without database', function () {
    $this->view('fpsplanificationstage::public.calendrier', calendrierPageData('calendrier'))
        ->assertSee('Portail des formations');
});

it('week view renders without database', function () {
    $this->view('fpsplanificationstage::public.calendrier', calendrierPageData('semaine'))
        ->assertSee('Semaine du 14 au 18 septembre 2026');
});

it('list view renders without database', function () {
    $this->view('fpsplanificationstage::public.calendrier', calendrierPageData('liste'))
        ->assertSee('Portail des formations');
});

it('skeletor style is present', function () {
    $path = base_path('Modules/FPSplanificationstage/resources/views/public/calendrier.blade.php');
    $source = file_get_contents($path);

    $this->assertIsString($source);
    $this->assertStringContainsString('PORTAIL_SKELETOR_STYLE_V1_3', $source);
    $this->assertStringContainsString('PORTAIL_VUE_SEMAINE_V1', $source);
    $this->assertStringContainsString('PORTAIL_URLS_RELATIVES_V1', $source);
});
