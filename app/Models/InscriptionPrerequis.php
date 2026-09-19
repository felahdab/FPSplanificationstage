<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscriptionPrerequis extends Model
{
    protected $table =
        'inscription_prerequis';

    protected $fillable = [
        'inscription_id',
        'prerequis_stage_id',
        'respecte',
    ];

    protected $casts = [
        'respecte' => 'boolean',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(
            Inscription::class
        );
    }

    public function prerequisStage(): BelongsTo
    {
        return $this->belongsTo(
            PrerequisStage::class
        );
    }
}