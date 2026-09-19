<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionStageHistorique extends Model
{
    protected $table =
        'session_stage_historiques';

    protected $fillable = [
        'session_stage_id',
        'type',
        'ancien_debut',
        'ancien_fin',
        'nouveau_debut',
        'nouveau_fin',
        'ancien_statut',
        'nouveau_statut',
        'motif',
        'commentaire',
        'meta',
    ];

    protected $casts = [
        'ancien_debut' =>
            'datetime',

        'ancien_fin' =>
            'datetime',

        'nouveau_debut' =>
            'datetime',

        'nouveau_fin' =>
            'datetime',

        'meta' =>
            'array',
    ];

    public function sessionStage(): BelongsTo
    {
        return $this->belongsTo(
            SessionStage::class,
            'session_stage_id'
        );
    }
}
