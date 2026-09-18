<?php

namespace Modules\PlanificationStages\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\PlanificationStages\Models\BesoinFormation;
use Modules\PlanificationStages\Models\Stage;

class PublicBesoinFormationController extends Controller
{
    public function create(): View
    {
        $stages =
            Stage::query()
                ->where(
                    'actif',
                    true
                )
                ->orderBy(
                    'libelle_court'
                )
                ->get();

        return view(
            'planificationstages::public.besoin-formation',
            [
                'stages' =>
                    $stages,
            ]
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate(
                [
                    'demandeur' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'contact_nom' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'contact_email' => [
                        'required',
                        'email',
                        'max:255',
                    ],

                    'contact_telephone' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'stage_id' => [
                        'required',
                        'integer',

                        Rule::exists(
                            'stages',
                            'id'
                        )->where(
                            fn ($query) =>
                                $query->where(
                                    'actif',
                                    true
                                )
                        ),
                    ],

                    'type_periode' => [
                        'required',
                        Rule::in([
                            'dates_fixes',
                            'plage',
                        ]),
                    ],

                    'date_debut_souhaitee' => [
                        'required',
                        'date',
                        'after_or_equal:today',
                    ],

                    'date_fin_souhaitee' => [
                        'required_unless:type_periode,dates_fixes',
                        'date',
                        'after_or_equal:date_debut_souhaitee',
                    ],

                    'priorite' => [
                        'required',
                        Rule::in([
                            'normale',
                            'haute',
                            'urgente',
                        ]),
                    ],

                    'nombre_stagiaires' => [
                        'required',
                        'integer',
                        'min:1',
                        'max:999',
                    ],

                    'commentaire' => [
                        'nullable',
                        'string',
                        'max:5000',
                    ],
                ]
            );

        $besoin =
            BesoinFormation::create([
                'stage_id' =>
                    $validated['stage_id'],

                'demandeur' =>
                    $validated['demandeur'],

                'contact_nom' =>
                    $validated['contact_nom'],

                'contact_email' =>
                    $validated['contact_email'],

                'contact_telephone' =>
                    $validated[
                        'contact_telephone'
                    ]
                    ?? null,

                'type_periode' =>
                    $validated[
                        'type_periode'
                    ],

                'date_debut_souhaitee' =>
                    $validated[
                        'date_debut_souhaitee'
                    ],

                'date_fin_souhaitee' =>
                    $validated[
                        'date_fin_souhaitee'
                    ],

                'priorite' =>
                    $validated['priorite'],

                'nombre_stagiaires' =>
                    $validated[
                        'nombre_stagiaires'
                    ],

                'statut' =>
                    'a_planifier',

                'session_stage_id' =>
                    null,

                'commentaire' =>
                    $validated['commentaire']
                    ?? null,

                'source' =>
                    'portail',

                'public_token' =>
                    (string) Str::uuid(),
            ]);

        return redirect()
            ->route(
                'planificationstages.public.besoin.confirmation',
                [
                    'token' =>
                        $besoin
                            ->public_token,
                ]
            );
    }

    public function confirmation(
        string $token
    ): View {
        $besoin =
            $this->findPublicBesoin(
                $token
            );

        return view(
            'planificationstages::public.besoin-formation-confirmation',
            [
                'besoin' =>
                    $besoin,
            ]
        );
    }

    public function suiviForm(): View
    {
        return view(
            'planificationstages::public.besoin-formation-suivi-recherche'
        );
    }

    public function rechercherSuivi(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate(
                [
                    'code_besoin' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'contact_email' => [
                        'required',
                        'email',
                        'max:255',
                    ],
                ]
            );

        $code =
            mb_strtoupper(
                trim(
                    $validated[
                        'code_besoin'
                    ]
                )
            );

        $email =
            trim(
                $validated[
                    'contact_email'
                ]
            );

        $besoin =
            BesoinFormation::query()
                ->where(
                    'source',
                    'portail'
                )
                ->where(
                    'code_besoin',
                    $code
                )
                ->where(
                    'contact_email',
                    $email
                )
                ->whereNotNull(
                    'public_token'
                )
                ->first();

        if (! $besoin) {
            throw ValidationException::withMessages([
                'code_besoin' =>
                    'La référence ou l’adresse e-mail ne correspond à aucune expression de besoin.',
            ]);
        }

        return redirect()
            ->route(
                'planificationstages.public.besoin.suivi',
                [
                    'token' =>
                        $besoin
                            ->public_token,
                ]
            );
    }

    public function suivi(
        string $token
    ): View {
        $besoin =
            $this->findPublicBesoin(
                $token
            );

        $besoin->load([
            'stage',
            'sessionStage.stage',
            'sessionStage.salle',
        ]);

        $statutPublic =
            match (
                $besoin->statut
            ) {
                'a_planifier' => [
                    'label' =>
                        'Demande reçue',

                    'description' =>
                        'Votre expression de besoin a bien été reçue et doit être étudiée par les gestionnaires.',

                    'type' =>
                        'info',
                ],

                'planifie' => [
                    'label' =>
                        'Session planifiée',

                    'description' =>
                        'Une session a été planifiée à partir de votre expression de besoin.',

                    'type' =>
                        'success',
                ],

                'conflit' => [
                    'label' =>
                        'En cours d’étude',

                    'description' =>
                        'La planification nécessite actuellement une étude complémentaire par les gestionnaires.',

                    'type' =>
                        'warning',
                ],

                'annule' => [
                    'label' =>
                        'Demande annulée',

                    'description' =>
                        'Cette expression de besoin est indiquée comme annulée.',

                    'type' =>
                        'danger',
                ],

                default => [
                    'label' =>
                        'En cours d’étude',

                    'description' =>
                        'Votre expression de besoin est en cours de traitement.',

                    'type' =>
                        'info',
                ],
            };

        return view(
            'planificationstages::public.besoin-formation-suivi',
            [
                'besoin' =>
                    $besoin,

                'statutPublic' =>
                    $statutPublic,
            ]
        );
    }

    private function findPublicBesoin(
        string $token
    ): BesoinFormation {
        return BesoinFormation::query()
            ->with('stage')
            ->where(
                'public_token',
                $token
            )
            ->where(
                'source',
                'portail'
            )
            ->firstOrFail();
    }
}