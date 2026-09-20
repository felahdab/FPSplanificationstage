<?php

use Modules\FPSplanificationstage\Services\SessionStageAlternativeFinder;

uses()->group('FPSplanificationstage');

it('empty suggestions produce empty message', function () {
    $service = new SessionStageAlternativeFinder();

    expect($service->formatForNotification([]))->toBe('');
});

it('rooms and dates are formatted for notification', function () {
    $service = new SessionStageAlternativeFinder();

    $message = $service->formatForNotification([
        'salles' => [
            ['id' => 1, 'label' => 'A101 — Salle Alpha (20 pers.)'],
            ['id' => 2, 'label' => 'B202 — Salle Bravo (30 pers.)'],
        ],
        'dates' => [
            ['debut' => '2026-09-21 08:00:00', 'fin' => '2026-09-21 16:00:00', 'label' => '21/09/2026 de 08:00 à 16:00'],
        ],
    ]);

    expect($message)->toBe(implode("\n", [
        'Salles disponibles :',
        '• A101 — Salle Alpha (20 pers.)',
        '• B202 — Salle Bravo (30 pers.)',
        '',
        'Créneaux disponibles :',
        '• 21/09/2026 de 08:00 à 16:00',
    ]));
});

it('only rooms do not add dates section', function () {
    $service = new SessionStageAlternativeFinder();

    $message = $service->formatForNotification([
        'salles' => [
            ['id' => 1, 'label' => 'Salle Alpha'],
        ],
        'dates' => [],
    ]);

    expect($message)->toBe("Salles disponibles :\n• Salle Alpha");
});

it('only dates do not add rooms section', function () {
    $service = new SessionStageAlternativeFinder();

    $message = $service->formatForNotification([
        'salles' => [],
        'dates' => [
            ['label' => '22/09/2026 de 08:00 à 16:00'],
        ],
    ]);

    expect($message)->toBe("Créneaux disponibles :\n• 22/09/2026 de 08:00 à 16:00");
});
