<?php

namespace Modules\FPSplanificationstage\Services;

use Modules\FPSplanificationstage\Models\Inscription;
use Modules\FPSplanificationstage\Models\SessionStage;

class StageDejaEffectueDetecto
{
    public function detecte(
        ?int $stagiaireId,
        ?int $sessionStageId,
        ?int $inscriptionAIgnorer = null
    ): bool {
        if (
            ! $stagiaireId
            || ! $sessionStageId
        ) {
            return false;
        }

        $stageId =
            SessionStage::query()
                ->whereKey(
                    $sessionStageId
                )
                ->value('stage_id');

        if (! $stageId) {
            return false;
        }

        return Inscription::query()
            ->where(
                'stagiaire_id',
                $stagiaireId
            )
            ->where(
                'presence',
                'present'
            )
            ->when(
                $inscriptionAIgnorer,
                fn ($query) =>
                    $query->whereKeyNot(
                        $inscriptionAIgnore
                    )
            )
            ->whereHas(
                'sessionStage',
                fn ($query) =>
                    $query->where(
                        'stage_id',
                        $stageId
                    )
            )
            ->exists();
    }
}
