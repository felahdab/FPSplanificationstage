<?php

namespace Modules\FPSplanificationstage\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\FPSplanificationstage\Models\Inscription;
use Modules\RH\Models\Marin;

class MarinDepuisInscriptionService
{
    /**
     * @return array{marin: Marin, created: bool}
     */
    public function creer(
        Inscription $inscription,
        array $data
    ): array {
        Gate::authorize(
            'rh::marins.create'
        );

        return DB::transaction(
            function () use (
                $inscription,
                $data
            ): array {
                $inscription =
                    Inscription::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $inscription->getKey()
                        );

                if ($inscription->stagiaire) {
                    return [
                        'marin' =>
                            $inscription->stagiaire,

                        'created' =>
                            false,
                    ];
                }

                $validated = Validator::make(
                    $data,
                    [
                        'nom' => [
                            'required',
                            'string',
                            'max:255',
                        ],

                        'prenom' => [
                            'required',
                            'string',
                            'max:255',
                        ],

                        'email' => [
                            'required',
                            'email',
                            'max:255',
                        ],

                        'matricule' => [
                            'nullable',
                            'string',
                            'max:20',
                        ],

                        'nid' => [
                            'nullable',
                            'string',
                            'max:15',
                        ],

                        'grade_id' => [
                            'nullable',
                            'integer',
                            'exists:rh_grades,id',
                        ],

                        'brevet_id' => [
                            'nullable',
                            'integer',
                            'exists:rh_brevets,id',
                        ],

                        'specialite_id' => [
                            'nullable',
                            'integer',
                            'exists:rh_specialites,id',
                        ],

                        'unite_id' => [
                            'nullable',
                            'integer',
                            'exists:rh_unites,id',
                        ],
                    ]
                )->validate();

                $marin =
                    $inscription
                        ->candidatUser
                        ? Marin::fromUser(
                            $inscription
                                ->candidatUser
                        )
                        : null;

                $marin ??=
                    app(
                        StagiaireResolver::class
                    )->find(
                        $validated
                    );

                $created = false;

                if (! $marin) {
                    $marin =
                        Marin::withoutGlobalScopes()
                            ->create([
                                ...$validated,

                                'user_id' =>
                                    $inscription
                                        ->candidat_user_id,
                            ]);

                    $created = true;
                }

                $inscription->update([
                    'stagiaire_id' =>
                        $marin->getKey(),
                ]);

                return [
                    'marin' =>
                        $marin,

                    'created' =>
                        $created,
                ];
            }
        );
    }
}
