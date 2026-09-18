<?php

namespace Modules\PlanificationStages\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Modules\PlanificationStages\Models\SalleOccupation;

class SalleOccupationChecker
{
    public function hasConflict(
        int $salleId,
        DateTimeInterface|string $debut,
        DateTimeInterface|string $fin
    ): bool {
        $start =
            Carbon::parse(
                $debut
            );

        $end =
            Carbon::parse(
                $fin
            );

        return SalleOccupation::query()
            ->where(
                'salle_id',
                $salleId
            )
            ->where(
                'debut',
                '<',
                $end
            )
            ->where(
                'fin',
                '>',
                $start
            )
            ->exists();
    }
}
