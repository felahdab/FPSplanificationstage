<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageFifModule extends Model
{
    protected $table = 'stage_fif_modules';

    protected $fillable = [
        'stage_id',
        'ordre',
        'module',
        'objectifs_competences',
        'source',
    ];

    protected $casts = [
        'ordre' => 'integer',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(
            Stage::class
        );
    }
}
