<?php

namespace Modules\PlanificationStages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instructeur extends Model
{
    protected $fillable = [
        'identifiant_interne',
        'nom',
        'prenom',
        'email',
        'actif',
        'salle_preferentielle',
        'commentaire',
        'import_match_key',
        'import_hash',
        'dernier_import_at',
        'salle_preferentielle_id',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'dernier_import_at' => 'datetime',
    ];

    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(
            Stage::class,
            'instructeur_stage'
        )
            ->withPivot([
                'role',
                'actif',
                'commentaire',
                'source',
                'dernier_import_at',
            ])
            ->withTimestamps();
    }
    public function sessions(): BelongsToMany
{
    return $this->belongsToMany(
        SessionStage::class,
        'instructeur_session_stage'
    )
        ->withPivot('role')
        ->withTimestamps();
}

    public function getNomCompletAttribute(): string
    {
        return trim(
            mb_strtoupper($this->nom) . ' ' . $this->prenom
        );
    }
    public function indisponibilites(): HasMany
{
    return $this->hasMany(IndisponibiliteInstructeur::class);
}
public function sallePreferentielle(): BelongsTo
{
    return $this->belongsTo(
        Salle::class,
        'salle_preferentielle_id'
    );
}
}