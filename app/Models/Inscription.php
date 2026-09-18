<?php

namespace Modules\PlanificationStages\Models;

use Modules\PlanificationStages\Models\Concerns\ResolvesStagiaire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscription extends Model
{
    
    use ResolvesStagiaire;
protected $table = 'inscriptions';

    protected $fillable = [
        'presence',
        'stagiaire_id',
        'code_inscription',
        'session_stage_id',

        // INSCRIPTION_IDENTITE_V1
        'matricule',
        'nid',
        'brevet',
        'specialite',
        'nom',
        'prenom',
        'grade',
        'unite',
        'email',
        'telephone',
        'statut',
        'nemo_recu',
        'nemo_recu_at',
        'derogation_demandee',
        'derogation_statut',
        'derogation_motif',
        'commentaire',
        'source',
    ];

    protected $casts = [
        'nemo_recu' =>
            'boolean',

        'nemo_recu_at' =>
            'datetime',

        'derogation_demandee' =>
            'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(
            function (
                Inscription $inscription
            ): void {
                if (
                    $inscription->nemo_recu
                    && ! $inscription->nemo_recu_at
                ) {
                    $inscription->nemo_recu_at =
                        now();
                }

                if (
                    ! $inscription->nemo_recu
                ) {
                    $inscription->nemo_recu_at =
                        null;
                }

                $statutsReservantUnePlace = [
                    'attente_nemo',
                    'confirmee',
                    'attente_derogation',
                ];

                if (
                    ! in_array(
                        $inscription->statut,
                        $statutsReservantUnePlace,
                        true
                    )
                ) {
                    return;
                }

                if (
                    ! $inscription->session_stage_id
                ) {
                    return;
                }

                $session =
                    SessionStage::find(
                        $inscription
                            ->session_stage_id
                    );

                if (
                    ! $session
                    || $session->capacite_max
                    === null
                ) {
                    return;
                }

                $query =
                    static::query()
                        ->where(
                            'session_stage_id',
                            $session->id
                        )
                        ->whereIn(
                            'statut',
                            $statutsReservantUnePlace
                        );

                if (
                    $inscription->exists
                    && $inscription->getKey()
                ) {
                    $query->whereKeyNot(
                        $inscription->getKey()
                    );
                }

                $placesOccupees =
                    $query->count();

                if (
                    $placesOccupees
                    >= $session->capacite_max
                ) {
                    $inscription->statut =
                        'liste_attente';
                }
            }
        );

        static::created(
            function (
                Inscription $inscription
            ): void {
                if (
                    ! $inscription
                        ->code_inscription
                ) {
                    $inscription
                        ->forceFill([
                            'code_inscription' =>
                                sprintf(
                                    'INS-%06d',
                                    $inscription->id
                                ),
                        ])
                        ->saveQuietly();
                }
            }
        );
    }

    public function sessionStage(): BelongsTo
    {
        return $this->belongsTo(
            SessionStage::class,
            'session_stage_id'
        );
    }

    public function prerequisReponses(): HasMany
    {
        return $this->hasMany(
            InscriptionPrerequis::class,
            'inscription_id'
        );
    }

    public function getNomCompletAttribute(): string
    {
        return trim(
            mb_strtoupper(
                $this->nom
            )
            . ' '
            . $this->prenom
        );
    }

    public function reserveUnePlace(): bool
    {
        return in_array(
            $this->statut,
            [
                'attente_nemo',
                'confirmee',
                'attente_derogation',
            ],
            true
        );
    }
    public function stagiaire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            Stagiaire::class,
            'stagiaire_id'
        );
    }

}