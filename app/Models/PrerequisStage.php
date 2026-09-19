<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrerequisStage extends Model
{
    protected $table = 'prerequis_stages';

    protected $fillable = [
        'stage_id',
        'ordre',
        'libelle',
        'obligatoire',
        'actif',
        'source',
        'source_colonne',
    ];

    protected $casts = [
        'obligatoire' => 'boolean',
        'actif' => 'boolean',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }
}