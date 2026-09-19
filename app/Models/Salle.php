<?php

namespace Modules\FPSplanificationstage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'capacite',
        'localisation',
        'type_salle',
        'equipements',
        'commentaire',
        'actif',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'actif' => 'boolean',
    ];

    public function getNomCompletAttribute(): string
    {
        if ($this->code) {
            return $this->code . ' — ' . $this->nom;
        }

        return $this->nom;
    }
    public function sessions(): HasMany
{
    return $this->hasMany(SessionStage::class);
}
}