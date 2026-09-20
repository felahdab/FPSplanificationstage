<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\FPSplanificationstage\Filament\Pages\Planning;
use Modules\FPSplanificationstage\Models\SessionStage;
use Modules\FPSplanificationstage\Models\Stage;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses()->group('FPSplanificationstage');

it('displays planned training sessions in the calendar', function () {
    app('url')->resolveMissingNamedRoutesUsing(
        fn (): string => '/testing/session-stages'
    );

    $stage = Stage::create([
        'code_stage' => 'STG-PLANNING-TEST',
        'libelle_court' => 'Formation planifiée',
        'actif' => true,
    ]);

    $session = SessionStage::create([
        'stage_id' => $stage->id,
        'debut' => Carbon::parse('2026-09-15 09:00'),
        'fin' => Carbon::parse('2026-09-15 17:00'),
        'statut' => 'planifiee',
    ]);

    $page = new Planning();
    $page->currentDate = '2026-09-15';

    $events = collect($page->calendarData()['days'])
        ->flatMap(fn (array $day): array => $day['events'])
        ->filter(fn (array $event): bool => $event['id'] === $session->id);

    expect($events)
        ->toHaveCount(1)
        ->and($events->first())
        ->toMatchArray([
            'id' => $session->id,
            'code' => $session->code_session,
            'stage' => 'Formation planifiée',
            'stage_code' => 'STG-PLANNING-TEST',
            'statut' => 'planifiee',
        ]);
});
