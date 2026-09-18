<?php

namespace Modules\PlanificationStages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionMessageTemplate extends Model
{
    protected $table = 'admission_message_templates';

    protected $fillable = [
        'stage_id',
        'nom',
        'objet',
        'corps',
        'format_admis',
        'format_refuse',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }
}
