<?php

namespace Modules\FPSplanificationstage\Services;

use Illuminate\Support\Facades\DB;
use Modules\FPSplanificationstage\Models\BesoinFormation;
use Modules\FPSplanificationstage\Models\SessionStage;

class BesoinSessionAllocationService
{
    public function affecter(
        BesoinFormation $besoin,
        SessionStage $session,
        int $effectifPrevu
    ): void {
        DB::transaction(function () use ($besoin, $session, $effectifPrevu): void {
            $besoin = BesoinFormation::query()
                ->lockForUpdate()
                ->findOrFail($besoin->getKey());

            $besoin->sessions()->syncWithoutDetaching([
                $session->getKey() => [
                    'effectif_prevu' => max(0, $effectifPrevu),
                ],
            ]);

            if ($besoin->session_stage_id === null) {
                $besoin->forceFill([
                    'session_stage_id' => $session->getKey(),
                ])->saveQuietly();
            }

            $this->synchroniserStatut($besoin);
        });
    }

    public function synchroniserStatut(BesoinFormation $besoin): void
    {
        $besoin->refresh();

        if ($besoin->statut === 'annule') {
            return;
        }

        $demande = max(
            0,
            (int) ($besoin->nombre_stagiaires ?? 0)
        );

        $planifie = (int) $besoin->sessions()
            ->where('session_stages.statut', '<>', 'annulee')
            ->sum('besoin_session_stage.effectif_prevu');

        if ($demande <= 0) {
            $nouveauStatut = $besoin->sessions()
                ->where('session_stages.statut', '<>', 'annulee')
                ->exists()
                    ? 'planifie'
                    : 'a_planifier';
        } elseif ($planifie <= 0) {
            $nouveauStatut = 'a_planifier';
        } elseif ($planifie < $demande) {
            $nouveauStatut = 'partiellement_planifie';
        } else {
            $nouveauStatut = 'planifie';
        }

        if ($besoin->statut !== $nouveauStatut) {
            $besoin->forceFill([
                'statut' => $nouveauStatut,
            ])->saveQuietly();
        }
    }

    public function resynchroniserTous(): array
    {
        $result = [
            'a_planifier' => 0,
            'partiellement_planifie' => 0,
            'planifie' => 0,
            'annule' => 0,
        ];

        BesoinFormation::query()
            ->orderBy('id')
            ->chunkById(200, function ($besoins) use (&$result): void {
                foreach ($besoins as $besoin) {
                    $this->synchroniserStatut($besoin);
                    $statut = $besoin->fresh()->statut;

                    if (array_key_exists($statut, $result)) {
                        $result[$statut]++;
                    }
                }
            });

        return $result;
    }
}
