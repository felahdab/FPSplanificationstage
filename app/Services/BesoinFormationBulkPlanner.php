<?php

namespace Modules\FPSplanificationstage\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Modules\FPSplanificationstage\Models\BesoinFormation;
use Modules\FPSplanificationstage\Models\SessionStage;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use Throwable;

class BesoinFormationBulkPlanner
{
    public function plannerMethodName(): string
    {
        $planner =
            app(
                BesoinFormationPlanner::class
            );

        return $this
            ->resolvePlannerMethod(
                $planner
            )
            ->getName();
    }

    public function plan(
        EloquentCollection $records
    ): array {
        $planner =
            app(
                BesoinFormationPlanner::class
            );

        $method =
            $this->resolvePlannerMethod(
                $planner
            );

        $records =
            $records
                ->sortBy(
                    function (
                        BesoinFormation $record
                    ): string {
                        /* PRIORITE_NEUTRALISEE_V1
                         * La priorité historique n'influence plus l'ordre.
                         */
                        $priority = 0;

                        $date =
                            $record
                                ->date_debut_souhaitee
                                ?->format('Y-m-d')
                            ?? '9999-12-31';

                        return sprintf(
                            '%d-%s-%010d',
                            $priority,
                            $date,
                            $record->getKey()
                        );
                    }
                )
                ->values();

        $result = [
            'selected' =>
                $records->count(),

            'planned' =>
                0,

            'conflicts' =>
                0,

            'skipped' =>
                0,

            'failed' =>
                0,

            'failed_codes' =>
                [],
        ];

        foreach ($records as $record) {
            $record->refresh();

            /*
             * Une sélection peut contenir un besoin déjà traité.
             * On ne touche qu'aux besoins réellement "À planifier".
             */
            if (
                $record->statut
                    !== 'a_planifier'
                || $record
                    ->session_stage_id
                    !== null
            ) {
                $result['skipped']++;

                continue;
            }

            try {
                /*
                 * On réutilise le moteur de planification unitaire
                 * déjà présent dans le module. Chaque besoin est
                 * traité l'un après l'autre : la planification
                 * suivante voit donc immédiatement les sessions
                 * créées par les précédentes.
                 */
                $singleResult =
                    $method->invoke(
                        $planner,
                        $record
                    );

                /*
                 * Certains moteurs retournent directement la
                 * SessionStage et laissent l'action appelante
                 * rattacher la session au besoin. On prend ce cas
                 * en charge sans modifier le comportement du
                 * moteur existant.
                 */
                if (
                    $singleResult
                    instanceof SessionStage
                ) {
                    $record->update([
                        'session_stage_id' =>
                            $singleResult
                                ->getKey(),

                        'statut' =>
                            'planifie',
                    ]);
                }

                /*
                 * Même principe si le moteur renvoie un tableau
                 * contenant une session.
                 */
                if (
                    is_array(
                        $singleResult
                    )
                ) {
                    $session =
                        $singleResult[
                            'session'
                        ]
                        ?? $singleResult[
                            'session_stage'
                        ]
                        ?? null;

                    if (
                        $session
                        instanceof SessionStage
                    ) {
                        $record->update([
                            'session_stage_id' =>
                                $session
                                    ->getKey(),

                            'statut' =>
                                'planifie',
                        ]);
                    }
                }

                $record->refresh();

                if (
                    $record
                        ->session_stage_id
                    !== null
                    || $record->statut
                        === 'planifie'
                ) {
                    $result['planned']++;

                    continue;
                }

                if (
                    $record->statut
                        === 'conflit'
                ) {
                    $result['conflicts']++;

                    continue;
                }

                /*
                 * Le moteur s'est exécuté sans erreur mais le besoin
                 * n'a pas été marqué planifié/conflit. On le classe
                 * en échec technique sans modifier son statut.
                 */
                $result['failed']++;

                $result['failed_codes'][] =
                    $record->code_besoin
                    ?? ('#' . $record->getKey());
            } catch (Throwable $exception) {
                /*
                 * Une erreur sur un besoin ne bloque pas les autres.
                 * On ne transforme pas automatiquement toute
                 * exception en "conflit", afin de ne pas masquer
                 * une vraie erreur technique.
                 */
                $result['failed']++;

                $result['failed_codes'][] =
                    $record->code_besoin
                    ?? ('#' . $record->getKey());

                report(
                    $exception
                );
            }
        }

        return $result;
    }

    private function resolvePlannerMethod(
        object $planner
    ): ReflectionMethod {
        $reflection =
            new ReflectionClass(
                $planner
            );

        /*
         * Noms usuels recherchés en priorité.
         */
        $preferredNames = [
            'planifier',
            'plan',
            'planifierBesoin',
            'planBesoin',
            'createSessionFromBesoin',
        ];

        foreach (
            $preferredNames
            as $name
        ) {
            if (
                ! $reflection
                    ->hasMethod(
                        $name
                    )
            ) {
                continue;
            }

            $method =
                $reflection
                    ->getMethod(
                        $name
                    );

            if (
                $this
                    ->isCompatiblePlannerMethod(
                        $method,
                        allowUntyped:
                            true
                    )
            ) {
                return $method;
            }
        }

        /*
         * Secours : recherche d'une méthode publique déclarée
         * directement sur BesoinFormationPlanner et recevant un
         * BesoinFormation comme premier paramètre.
         */
        foreach (
            $reflection
                ->getMethods(
                    ReflectionMethod::IS_PUBLIC
                )
            as $method
        ) {
            if (
                $method
                    ->getDeclaringClass()
                    ->getName()
                !== BesoinFormationPlanner::class
            ) {
                continue;
            }

            if (
                $method->getName()
                    === '__construct'
            ) {
                continue;
            }

            if (
                $this
                    ->isCompatiblePlannerMethod(
                        $method,
                        allowUntyped:
                            false
                    )
            ) {
                return $method;
            }
        }

        throw new RuntimeException(
            'Aucune méthode publique compatible n’a été trouvée dans BesoinFormationPlanner. '
            . 'La planification en masse n’a pas été exécutée.'
        );
    }

    private function isCompatiblePlannerMethod(
        ReflectionMethod $method,
        bool $allowUntyped
    ): bool {
        if (
            ! $method->isPublic()
            || $method->isStatic()
            || $method
                ->getNumberOfParameters()
                < 1
            || $method
                ->getNumberOfRequiredParameters()
                > 1
        ) {
            return false;
        }

        $parameter =
            $method
                ->getParameters()[0];

        $type =
            $parameter
                ->getType();

        if ($type === null) {
            return $allowUntyped;
        }

        if (
            ! $type
                instanceof ReflectionNamedType
        ) {
            return false;
        }

        $name =
            ltrim(
                $type->getName(),
                '\\'
            );

        return
            $name
                === BesoinFormation::class
            || is_a(
                BesoinFormation::class,
                $name,
                true
            );
    }
}
