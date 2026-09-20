<?php

namespace Modules\FPSplanificationstage\Services;

use Modules\RH\Models\Marin;

class StagiaireResolver
{
    public function resolve(array $identity): Marin
    {
        $nom = $this->clean($identity['nom'] ?? null);
        $prenom = $this->clean($identity['prenom'] ?? null);

        if (
            $nom === null
            || $prenom === null
        ) {
            throw new \InvalidArgumentException('Le nom et le prénom du marin sont obligatoires.');
        }

        $nid = $this->clean($identity['nid'] ?? null);
        $matricule = $this->clean($identity['matricule'] ?? null);

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
                Marin::withoutGlobalScopes()
                    ->whereRaw(
                        'UPPER(TRIM(nid)) = ?',
                        [
                            mb_strtoupper($nid),
                        ]
                    )
                    ->first();
        }

        if (
            ! $stagiaire
            && $matricule !== null
        ) {
            $stagiaire =
                Marin::withoutGlobalScopes()
                    ->whereRaw(
                        'UPPER(TRIM(matricule)) = ?',
                        [
                            mb_strtoupper($matricule),
                        ]
                    )
                    ->first();
        }

        if (! $stagiaire) {
            $candidats =
                Marin::withoutGlobalScopes()
                    ->whereRaw(
                        'UPPER(TRIM(nom)) = ?',
                        [
                            mb_strtoupper($nom),
                        ]
                    )
                    ->whereRaw(
                        'UPPER(TRIM(prenom)) = ?',
                        [
                            mb_strtoupper($prenom),
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
                Marin::withoutGlobalScopes()->create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'nid' => $nid,
                    'matricule' =>
                        $matricule,
                    'email' => $this->clean($identity['email'] ?? null),
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
                    'email',
                ]
                as $field
            ) {
                $value = $this->clean($identity[$field] ?? null);

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
