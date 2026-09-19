<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalleOccupation extends Model
{
    protected $table =
        'salle_occupations';

    protected $fillable = [
        'salle_id',
        'libelle',
        'debut',
        'fin',
        'source',
        'source_fichier',
        'excel_sheet',
        'excel_range',
        'import_key',
        'imported_at',
    ];

    protected $casts = [
        'debut' => 'datetime',
        'fin' => 'datetime',
        'imported_at' => 'datetime',
    ];

    public function salle(): BelongsTo
    {
        return $this->belongsTo(
            Salle::class
        );
    }
}
