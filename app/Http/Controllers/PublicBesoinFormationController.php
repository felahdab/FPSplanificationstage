<?php

namespace Modules\FPSplanificationstage\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\FPSplanificationstage\Models\BesoinFormation;
use Modules\FPSplanificationstage\Models\Stage;
use Modules\FPSplanificationstage\Services\BesoinPeriodeService;
use Modules\FPSplanificationstage\Services\StagiaireResolver;
use Modules\RH\Models\Marin;
use Modules\RH\Models\Unite;

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
            'fpsplanificationstage::public.besoin-formation',
            [
                'stages' =>
                    $stages,

                'unites' =>
                    $this->uniteLabels(),

                'demandeur' =>
                    $this->demandeurFor(
                        auth()->user()
                    ),
            ]
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        if (
            ! $request->has('besoins')
            && $request->has('stage_id')
        ) {
            $request->merge([
                'besoins' => [[
                    'stage_id' =>
                        $request->input('stage_id'),

                    'type_periode' =>
                        $request->input('type_periode'),

                    'date_debut_souhaitee' =>
                        $request->input('date_debut_souhaitee'),

                    'date_fin_souhaitee' =>
                        $request->input('date_fin_souhaitee'),

                    'nombre_stagiaires' =>
                        $request->input('nombre_stagiaires'),

                    'commentaire' =>
                        $request->input('commentaire'),
                ]],
            ]);
        }

        $validated =
            $request->validate(
                [
                    'demandeur' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::exists(
                            Unite::class,
                            'libelle_long'
                        ),
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

                    'besoins' => [
                        'required',
                        'array',
                        'min:1',
                        'max:20',
                    ],

                    'besoins.*.stage_id' => [
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

                    'besoins.*.type_periode' => [
                        'required',
                        Rule::in([
                            'dates_fixes',
                            'plage',
                            'plage_demarrage',
                        ]),
                    ],

                    'besoins.*.date_debut_souhaitee' => [
                        'required',
                        'date',
                        'after_or_equal:today',
                    ],

                    'besoins.*.date_fin_souhaitee' => [
                        'nullable',
                        'date',
                    ],

                    'besoins.*.nombre_stagiaires' => [
                        'required',
                        'integer',
                        'min:1',
                        'max:999',
                    ],

                    'besoins.*.commentaire' => [
                        'nullable',
                        'string',
                        'max:5000',
                    ],
                ]
            );

        foreach (
            $validated['besoins']
            as $index => $besoinData
        ) {
            $periodeError =
                BesoinPeriodeService::validateValues(
                    $besoinData['stage_id'],
                    $besoinData['type_periode'],
                    $besoinData['date_debut_souhaitee'],
                    $besoinData['date_fin_souhaitee'] ?? null
                );

            if ($periodeError !== null) {
                throw ValidationException::withMessages([
                    "besoins.$index.date_fin_souhaitee" =>
                        $periodeError,
                ]);
            }
        }

        $created =
            DB::transaction(
                function () use (
                    $validated
                ): array {
                    $created = [];

                    foreach (
                        $validated['besoins']
                        as $besoinData
                    ) {
                        $besoin =
                            BesoinFormation::create([
                                'stage_id' =>
                                    $besoinData[
                                        'stage_id'
                                    ],

                                'demandeur' =>
                                    $validated[
                                        'demandeur'
                                    ],

                                'contact_nom' =>
                                    $validated[
                                        'contact_nom'
                                    ],

                                'contact_email' =>
                                    $validated[
                                        'contact_email'
                                    ],

                                'contact_telephone' =>
                                    $validated[
                                        'contact_telephone'
                                    ]
                                    ?? null,

                                'type_periode' =>
                                    $besoinData[
                                        'type_periode'
                                    ],

                                'date_debut_souhaitee' =>
                                    $besoinData[
                                        'date_debut_souhaitee'
                                    ],

                                'date_fin_souhaitee' =>
                                    $besoinData[
                                        'date_fin_souhaitee'
                                    ]
                                    ?? null,

                                'priorite' =>
                                    'normale',

                                'nombre_stagiaires' =>
                                    $besoinData[
                                        'nombre_stagiaires'
                                    ],

                                'statut' =>
                                    'a_planifier',

                                'session_stage_id' =>
                                    null,

                                'commentaire' =>
                                    $besoinData[
                                        'commentaire'
                                    ]
                                    ?? null,

                                'source' =>
                                    'portail',

                                'public_token' =>
                                    (string) Str::uuid(),
                            ]);

                        $besoin->load('stage');

                        $created[] = [
                            'code_besoin' =>
                                $besoin
                                    ->code_besoin,

                            'public_token' =>
                                $besoin
                                    ->public_token,

                            'stage' =>
                                $besoin
                                    ->stage
                                    ?->libelle_court
                                ?? 'Stage',
                        ];
                    }

                    return $created;
                }
            );

        $first =
            $created[0];

        return redirect()
            ->to(
                '/apps/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/'
                . $first['public_token']
                . '/confirmation'
            )
            ->with(
                'besoins_crees',
                $created
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
            'fpsplanificationstage::public.besoin-formation-confirmation',
            [
                'besoin' =>
                    $besoin,
            ]
        );
    }

    public function suiviForm(): View
    {
        return view(
            'fpsplanificationstage::public.besoin-formation-suivi-recherche'
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

        return redirect()->to(
            '/apps/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/'
            . $besoin->public_token
            . '/suivi'
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
            'fpsplanificationstage::public.besoin-formation-suivi',
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

    /** @return array<int, string> */
    private function uniteLabels(): array
    {
        return Unite::query()
            ->whereNotNull(
                'libelle_long'
            )
            ->where(
                'libelle_long',
                '<>',
                ''
            )
            ->orderBy(
                'libelle_long'
            )
            ->pluck(
                'libelle_long'
            )
            ->unique()
            ->values()
            ->all();
    }

    private function demandeurFor(
        ?User $user
    ): ?string {
        if (! $user) {
            return null;
        }

        $marin =
            Marin::fromUser(
                $user
            )
            ?? app(
                StagiaireResolver::class
            )->find([
                'nom' =>
                    $user->nom,

                'prenom' =>
                    $user->prenom,

                'email' =>
                    $user->email,
            ]);

        $unite =
            $marin?->unite
            ?? $this->findMindefUnite(
                data_get(
                    $user->getMindefConnectInformations(),
                    'main_department_number'
                )
            );

        return $unite?->libelle_long;
    }

    private function findMindefUnite(
        mixed $mindefUnite
    ): ?Unite {
        $mindefUnite = trim(
            (string) $mindefUnite
        );

        if ($mindefUnite === '') {
            return null;
        }

        return Unite::query()
            ->where(
                'libannudef',
                $mindefUnite
            )
            ->orWhere(
                'libelle_long',
                $mindefUnite
            )
            ->orWhere(
                'libelle_court',
                $mindefUnite
            )
            ->first();
    }
}
