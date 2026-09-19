<?php

namespace Modules\FPSplanificationstage\Services;

use Modules\FPSplanificationstage\Models\Inscription;
use Modules\FPSplanificationstage\Models\Stagiaire;

class StagiaireResolver
{
    public function resolveForInscription(
        Inscription $inscription
    ): ?Stagiaire {
        $nom = $this->clean(
            $inscription->nom
        );

        $prenom = $this->clean(
            $inscription->prenom
        );

        if (
            $nom === null
            || $prenom === null
        ) {
            return null;
        }

        $nid = $this->clean(
            $inscription->nid
        );

        $matricule = $this->clean(
            $inscription->matricule
        );

        /*
         * Ordre de fiabilité :
         * 1. NID
         * 2. Matricule
         * 3. Nom + prénom uniquement si le résultat
         *    est sans ambiguïté.
         */
        $stagiaire = null;

        if ($nid !== null) {
            $stagiaire =
                Stagiaire::query()
                    ->whereRaw(
                        'UPPER(TRIM(nid)) = ?',
                        [
                            mb_strtoupper(
                                $nid
                            ),
                        ]
                    )
                    ->first();
        }

        if (
            ! $stagiaire
            && $matricule !== null
        ) {
            $stagiaire =
                Stagiaire::query()
                    ->whereRaw(
                        'UPPER(TRIM(matricule)) = ?',
                        [
                            mb_strtoupper(
                                $matricule
                            ),
                        ]
                    )
                    ->first();
        }

        if (! $stagiaire) {
            $candidats =
                Stagiaire::query()
                    ->whereRaw(
                        'UPPER(TRIM(nom)) = ?',
                        [
                            mb_strtoupper(
                                $nom
                            ),
                        ]
                    )
                    ->whereRaw(
                        'UPPER(TRIM(prenom)) = ?',
                        [
                            mb_strtoupper(
                                $prenom
                            ),
                        ]
                    )
                    ->limit(2)
                    ->get();

            if (
                $candidats->count()
                === 1
            ) {
                $stagiaire =
                    $candidats->first();
            }
        }

        if (! $stagiaire) {
            $stagiaire =
                Stagiaire::create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'grade' =>
                        $this->clean(
                            $inscription->grade
                        ),
                    'brevet' =>
                        $this->clean(
                            $inscription->brevet
                        ),
                    'specialite' =>
                        $this->clean(
                            $inscription->specialite
                        ),
                    'nid' => $nid,
                    'matricule' =>
                        $matricule,
                    'unite' =>
                        $this->clean(
                            $inscription->unite
                        ),
                    'email' =>
                        $this->clean(
                            $inscription->email
                        ),
                    'telephone' =>
                        $this->clean(
                            $inscription->telephone
                        ),
                ]);
        } else {
            /*
             * Les informations descriptives sont mises
             * à jour avec les données les plus récentes.
             *
             * Pour NID / matricule :
             * on complète un champ vide, mais on
             * n'écrase jamais un identifiant déjà connu.
             */
            $data = [
                'nom' => $nom,
                'prenom' => $prenom,
            ];

            foreach (
                [
                    'grade',
                    'brevet',
                    'specialite',
                    'unite',
                    'email',
                    'telephone',
                ]
                as $field
            ) {
                $value =
                    $this->clean(
                        $inscription->{$field}
                    );

                if ($value !== null) {
                    $data[$field] =
                        $value;
                }
            }

            if (
                $this->clean(
                    $stagiaire->nid
                )
                === null
                && $nid !== null
            ) {
                $data['nid'] = $nid;
            }

            if (
                $this->clean(
                    $stagiaire->matricule
                )
                === null
                && $matricule !== null
            ) {
                $data['matricule'] =
                    $matricule;
            }

            $stagiaire->update(
                $data
            );
        }

        if (
            (int) $inscription
                ->stagiaire_id
            !== (int) $stagiaire->id
        ) {
            $inscription
                ->forceFill([
                    'stagiaire_id' =>
                        $stagiaire->id,
                ])
                ->saveQuietly();
        }

        return $stagiaire;
    }

    private function clean(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }
}
