<?php

namespace Modules\FPSplanificationstage\Models\Concerns;

use Modules\FPSplanificationstage\Models\Inscription;
use Modules\FPSplanificationstage\Services\StagiaireResolver;

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
