<?php

namespace Modules\FPSplanificationstage\Services;

use Modules\RH\Models\Marin;

class StagiaireResolve
{
    public function find(
        array $identity
    ): ?Marin {
        $nom = $this->clean(
            $identity['nom'] ?? null
        );

        $prenom = $this->clean(
            $identity['prenom'] ?? null
        );

        $nid = $this->clean(
            $identity['nid'] ?? null
        );

        $matricule = $this->clean(
            $identity['matricule'] ?? null
        );

        $email = $this->clean(
            $identity['email'] ?? null
        );

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

        if (
            ! $stagiaire
            && $email !== null
        ) {
            $stagiaire =
                Marin::withoutGlobalScopes()
                    ->whereRaw(
                        'LOWER(TRIM(email)) = ?',
                        [
                            mb_strtolower($email),
                        ]
                    )
                    ->first();
        }

        if (
            ! $stagiaire
            && $nom !== null
            && $prenom !== null
        ) {
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

        return $stagiaire;
    }

    public function resolve(
        array $identity
    ): Marin {
        $nom = $this->clean(
            $identity['nom'] ?? null
        );

        $prenom = $this->clean(
            $identity['prenom'] ?? null
        );

        if (
            $nom === null
            || $prenom === null
        ) {
            throw new \InvalidArgumentException(
                'Le nom et le prénom du marin sont obligatoires.'
            );
        }

        $nid = $this->clean(
            $identity['nid'] ?? null
        );

        $matricule = $this->clean(
            $identity['matricule'] ?? null
        );

        $stagiaire = $this->find(
            $identity
        );

        if (! $stagiaire) {
            return Marin::withoutGlobalScopes()
                ->create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'nid' => $nid,
                    'matricule' =>
                        $matricule,
                    'email' => $this->clean(
                        $identity['email'] ?? null
                    ),
                ]);
        }

        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
        ];

        $email = $this->clean(
            $identity['email'] ?? null
        );

        if ($email !== null) {
            $data['email'] = $email;
        }

        if (
            $this->clean(
                $stagiaire->nid
            ) === null
            && $nid !== null
        ) {
            $data['nid'] = $nid;
        }

        if (
            $this->clean(
                $stagiaire->matricule
            ) === null
            && $matricule !== null
        ) {
            $data['matricule'] =
                $matricule;
        }

        $stagiaire->update(
            $data
        );

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
