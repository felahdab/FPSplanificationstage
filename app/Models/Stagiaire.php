<?php

namespace Modules\PlanificationStages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stagiaire extends Model
{
    protected $table = 'stagiaires';

    protected $fillable = [
        'nom',
        'prenom',
        'grade',
        'brevet',
        'specialite',
        'nid',
        'matricule',
        'unite',
        'email',
        'telephone',
    ];

    public function inscriptions(): HasMany
    {
        return $this->hasMany(
            Inscription::class,
            'stagiaire_id'
        );
    }

    public function getNomCompletAttribute(): string
    {
        return trim(
            ($this->nom ?? '')
            . ' '
            . ($this->prenom ?? '')
        );
    }
}
