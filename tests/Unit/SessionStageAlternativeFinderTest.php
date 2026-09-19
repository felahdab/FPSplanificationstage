<?php

namespace Modules\FPSplanificationstage\Tests\Unit;

use Modules\FPSplanificationstage\Services\SessionStageAlternativeFinder;
use PHPUnit\Framework\TestCase;

class SessionStageAlternativeFinderTest extends TestCase
{
    public function test_empty_suggestions_produce_empty_message(): void
    {
        $service = new SessionStageAlternativeFinder();

        self::assertSame(
            '',
            $service->formatForNotification([])
        );
    }

    public function test_rooms_and_dates_are_formatted_for_notification(): void
    {
        $service = new SessionStageAlternativeFinder();

        $message = $service->formatForNotification([
            'salles' => [
                [
                    'id' => 1,
                    'label' => 'A101 — Salle Alpha (20 pers.)',
                ],
                [
                    'id' => 2,
                    'label' => 'B202 — Salle Bravo (30 pers.)',
                ],
            ],
            'dates' => [
                [
                    'debut' => '2026-09-21 08:00:00',
                    'fin' => '2026-09-21 16:00:00',
                    'label' => '21/09/2026 de 08:00 à 16:00',
                ],
            ],
        ]);

        self::assertSame(
            implode("\n", [
                'Salles disponibles :',
                '• A101 — Salle Alpha (20 pers.)',
                '• B202 — Salle Bravo (30 pers.)',
                '',
                'Créneaux disponibles :',
                '• 21/09/2026 de 08:00 à 16:00',
            ]),
            $message
        );
    }

    public function test_only_rooms_do_not_add_dates_section(): void
    {
        $service = new SessionStageAlternativeFinder();

        $message = $service->formatForNotification([
            'salles' => [
                [
                    'id' => 1,
                    'label' => 'Salle Alpha',
                ],
            ],
            'dates' => [],
        ]);

        self::assertSame(
            "Salles disponibles :\n• Salle Alpha",
            $message
        );
    }

    public function test_only_dates_do_not_add_rooms_section(): void
    {
        $service = new SessionStageAlternativeFinder();

        $message = $service->formatForNotification([
            'salles' => [],
            'dates' => [
                [
                    'label' => '22/09/2026 de 08:00 à 16:00',
                ],
            ],
        ]);

        self::assertSame(
            "Créneaux disponibles :\n• 22/09/2026 de 08:00 à 16:00",
            $message
        );
    }
}
