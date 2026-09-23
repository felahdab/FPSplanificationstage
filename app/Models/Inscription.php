<?php

namespace Modules\FPSplanificationstage\Models;

use Modules\RH\Models\Marin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscription extends Model
{
    
protected $table = 'inscriptions';

    protected $fillable = [
        'presence',
        'stagiaire_id',
        'code_inscription',
        'session_stage_id',

        'statut',
        'nemo_recu',
        'nemo_recu_at',
        'derogation_demandee',
        'derogation_statut',
        'derogation_motif',
        'derogation_document',
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

                $derogationTerminee =
                    $inscription->statut
                    === 'attente_derogation'
                    && in_array(
                        $inscription
                            ->derogation_statut,
                        [
                            'acceptee',
                            'refusee',
                        ],
                        true
                    );

                if (
                    $derogationTerminee
                    && $inscription
                        ->derogation_statut
                    === 'refusee'
                ) {
                    $inscription->statut =
                        'refusee';
                } elseif (
                    blank($inscription->statut)
                    || $derogationTerminee
                    || in_array(
                        $inscription->statut,
                        [
                            'attente_nemo',
                            'confirmee',
                        ],
                        true
                    )
                ) {
                    $inscription->statut =
                        $inscription->nemo_recu
                            ? 'confirmee'
                            : 'attente_nemo';
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
        $stagiaire = $this->stagiaire;

        return trim(
            mb_strtoupper(
                $stagiaire?->nom ?? ''
            )
            . ' '
            . ($stagiaire?->prenom ?? '')
        );
    }

    public function getNomAttribute(): ?string
    {
        return $this->stagiaire?->nom;
    }

    public function getPrenomAttribute(): ?string
    {
        return $this->stagiaire?->prenom;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->stagiaire?->email;
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
            Marin::class,
            'stagiaire_id'
        )->withoutGlobalScopes();
    }

}
