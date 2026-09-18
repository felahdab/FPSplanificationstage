<?php

namespace Modules\PlanificationStages\Models\Concerns;

use Modules\PlanificationStages\Models\Inscription;
use Modules\PlanificationStages\Services\StagiaireResolver;

trait ResolvesStagiaire
{
    public static function bootResolvesStagiaire(): void
    {
        static::saved(
            function (
                Inscription $inscription
            ): void {
                app(
                    StagiaireResolver::class
                )->resolveForInscription(
                    $inscription
                );
            }
        );
    }
}
