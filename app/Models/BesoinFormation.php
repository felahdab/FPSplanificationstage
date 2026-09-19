<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BesoinFormation extends Model
{
    protected $table = 'besoin_formations';

    protected $fillable = [
        'code_besoin',
        'stage_id',
        'demandeur',
        'contact_nom',
        'contact_email',
        'contact_telephone',
        'type_periode',
        'date_debut_souhaitee',
        'date_fin_souhaitee',
        'priorite',
        'nombre_stagiaires',
        'statut',
        'session_stage_id',
        'commentaire',
        'source',
        'public_token',
        'import_match_key',
        'import_hash',
        'dernier_import_at',
    ];

    protected $casts = [
        'date_debut_souhaitee' =>
            'date',

        'date_fin_souhaitee' =>
            'date',

        'nombre_stagiaires' =>
            'integer',

        'dernier_import_at' =>
            'datetime',
    ];

    protected static function booted(): void
    {
        
        /*
         * BESOIN_PERIODE_DUREE_STAGE_V1_2
         * Validation centrale : admin, portail public, imports et code.
         */
        static::saving(
            function (BesoinFormation $besoin): void {
                $message =
                    \Modules\FPSplanificationstage\Services\BesoinPeriodeService::validateValues(
                        $besoin->stage_id,
                        $besoin->type_periode,
                        $besoin->date_debut_souhaitee,
                        $besoin->date_fin_souhaitee
                    );

                if ($message !== null) {
                    $field =
                        $besoin->type_periode === 'dates_fixes'
                            ? 'date_debut_souhaitee'
                            : 'date_fin_souhaitee';

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        $field => $message,
                    ]);
                }

                if ($besoin->type_periode === 'dates_fixes') {
                    $fin =
                        \Modules\FPSplanificationstage\Services\BesoinPeriodeService::calculatedFixedEndDate(
                            $besoin->stage_id,
                            $besoin->date_debut_souhaitee
                        );

                    if (! $fin) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'date_debut_souhaitee' =>
                                'Impossible de calculer la date de fin à partir de la durée du stage.',
                        ]);
                    }

                    /*
                     * On stocke la fin calculée même si une ancienne migration
                     * a rendu la colonne nullable. Cela garantit un affichage
                     * et un export cohérents.
                     */
                    $besoin->date_fin_souhaitee = $fin->format('Y-m-d');
                }
            }
        );
static::created(
            function (
                BesoinFormation $besoin
            ): void {
                if (! $besoin->code_besoin) {
                    $besoin
                        ->forceFill([
                            'code_besoin' =>
                                sprintf(
                                    'BES-%06d',
                                    $besoin->id
                                ),
                        ])
                        ->saveQuietly();
                }
            }
        );
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(
            Stage::class
        );
    }

    public function sessionStage(): BelongsTo
    {
        return $this->belongsTo(
            SessionStage::class,
            'session_stage_id'
        );
    }

    /* RELATION_MULTI_SESSIONS_BESOIN */
    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(
            SessionStage::class,
            'besoin_session_stage',
            'besoin_formation_id',
            'session_stage_id'
        )
            ->withPivot('effectif_prevu')
            ->withTimestamps();
    }

    public function getEffectifPlanifieAttribute(): int
    {
        return (int) $this->sessions()
            ->where('session_stages.statut', '<>', 'annulee')
            ->sum('besoin_session_stage.effectif_prevu');
    }

    public function getEffectifRestantAttribute(): int
    {
        $demande = max(
            0,
            (int) ($this->nombre_stagiaires ?? 0)
        );

        return max(
            0,
            $demande - $this->effectif_planifie
        );
    }
}