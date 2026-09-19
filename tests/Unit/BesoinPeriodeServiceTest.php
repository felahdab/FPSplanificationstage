<?php

namespace Modules\FPSplanificationstage\Tests\Unit;

use Modules\FPSplanificationstage\Services\BesoinPeriodeService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BesoinPeriodeServiceTest extends TestCase
{
    public function test_allowed_types_are_exactly_the_three_supported_modes(): void
    {
        self::assertSame(
            [
                BesoinPeriodeService::TYPE_DATE_FIXE,
                BesoinPeriodeService::TYPE_PLAGE_DISPONIBILITE,
                BesoinPeriodeService::TYPE_PLAGE_DEMARRAGE,
            ],
            BesoinPeriodeService::allowedTypes()
        );
    }

    #[DataProvider('rangeModeProvider')]
    public function test_range_mode_detection(
        ?string $type,
        bool $expected
    ): void {
        self::assertSame(
            $expected,
            BesoinPeriodeService::isRangeMode($type)
        );
    }

    public static function rangeModeProvider(): array
    {
        return [
            'date fixe' => [
                BesoinPeriodeService::TYPE_DATE_FIXE,
                false,
            ],
            'plage disponibilité' => [
                BesoinPeriodeService::TYPE_PLAGE_DISPONIBILITE,
                true,
            ],
            'plage démarrage' => [
                BesoinPeriodeService::TYPE_PLAGE_DEMARRAGE,
                true,
            ],
            'null' => [
                null,
                false,
            ],
            'type inconnu' => [
                'inconnu',
                false,
            ],
        ];
    }

    #[DataProvider('durationProvider')]
    public function test_minimum_working_days(
        float|int|string|null $duration,
        int $expected
    ): void {
        self::assertSame(
            $expected,
            BesoinPeriodeService::minimumWorkingDays($duration)
        );
    }

    public static function durationProvider(): array
    {
        return [
            'null' => [null, 0],
            'zéro' => [0, 0],
            'négatif' => [-1, 0],
            'un jour' => [1, 1],
            'deux jours' => [2, 2],
            'deux jours et demi' => [2.5, 3],
            'chaîne décimale' => ['4.2', 5],
            'demi-journée' => [0.5, 1],
        ];
    }

    #[DataProvider('workingDaysProvider')]
    public function test_count_working_days(
        mixed $start,
        mixed $end,
        int $expected
    ): void {
        self::assertSame(
            $expected,
            BesoinPeriodeService::countWorkingDays(
                $start,
                $end
            )
        );
    }

    public static function workingDaysProvider(): array
    {
        return [
            'semaine complète lundi vendredi' => [
                '2026-09-14',
                '2026-09-18',
                5,
            ],
            'vendredi à lundi' => [
                '2026-09-18',
                '2026-09-21',
                2,
            ],
            'samedi et dimanche uniquement' => [
                '2026-09-19',
                '2026-09-20',
                0,
            ],
            'samedi à lundi' => [
                '2026-09-19',
                '2026-09-21',
                1,
            ],
            'même lundi' => [
                '2026-09-21',
                '2026-09-21',
                1,
            ],
            'fin avant début' => [
                '2026-09-22',
                '2026-09-21',
                0,
            ],
            'début absent' => [
                null,
                '2026-09-21',
                0,
            ],
            'fin absente' => [
                '2026-09-21',
                null,
                0,
            ],
        ];
    }
}
