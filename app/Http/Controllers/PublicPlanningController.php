<?php

namespace Modules\FPSplanificationstage\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\FPSplanificationstage\Models\SessionStage;

class PublicPlanningController extends Controller
{
    /*
     * PORTAIL_VUES_RECHERCHE_STAGES_V1
     *
     * Deux modes publics :
     * - calendrier mensuel ;
     * - liste chronologique de toutes les sessions à venir.
     *
     * Recherche combinée sur :
     * - libelle_court
     * - libelle_long
     * - lieux_formation        (FPS / lieu)
     * - centre_formation       (complément FPS / centre)
     * - service_emetteur       (service responsable)
     */
    public function index(
        Request $request
    ): View {
        $viewMode =
            (string) $request
                ->query(
                    'vue',
                    'calendrier'
                );

        if (
            ! in_array(
                $viewMode,
                [
                    'calendrier',
                    'semaine',
                    'liste',
                ],
                true
            )
        ) {
            $viewMode =
                'calendrier';
        }

        $searchTerm =
            trim(
                (string) $request
                    ->query(
                        'q',
                        ''
                    )
            );

        $moisDemande =
            $request->query(
                'mois'
            );

        try {
            $reference =
                $moisDemande
                    ? Carbon::createFromFormat(
                        'Y-m',
                        $moisDemande
                    )
                        ->startOfMonth()
                    : Carbon::today()
                        ->startOfMonth();
        } catch (\Throwable) {
            $reference =
                Carbon::today()
                    ->startOfMonth();
        }

        $monthStart =
            $reference
                ->copy()
                ->startOfMonth();

        $monthEnd =
            $reference
                ->copy()
                ->endOfMonth();

        $gridStart =
            $monthStart
                ->copy()
                ->startOfWeek(
                    Carbon::MONDAY
                )
                ->startOfDay();

        $gridEnd =
            $monthEnd
                ->copy()
                ->endOfWeek(
                    Carbon::SUNDAY
                )
                ->endOfDay();

        /*
         * Calendrier mensuel.
         *
         * Si une recherche est active, seules les sessions
         * du stage correspondant apparaissent dans le mois.
         */
        $calendarQuery =
            $this->publicSessionsQuery();

        $this->applyStageSearch(
            $calendarQuery,
            $searchTerm
        );

        $sessions =
            $calendarQuery
                ->where(
                    'debut',
                    '<=',
                    $gridEnd
                )
                ->where(
                    'fin',
                    '>=',
                    $gridStart
                )
                ->orderBy('debut')
                ->get();

        $days = [];

        $cursor =
            $gridStart->copy();

        while (
            $cursor->lte(
                $gridEnd
            )
        ) {
            $day =
                $cursor->copy();

            $dayStart =
                $day
                    ->copy()
                    ->startOfDay();

            $dayEnd =
                $day
                    ->copy()
                    ->endOfDay();

            $events = [];

            foreach (
                $sessions
                as $session
            ) {
                if (
                    ! $session
                        ->debut
                        ->lte($dayEnd)
                    || ! $session
                        ->fin
                        ->gte($dayStart)
                ) {
                    continue;
                }

                $events[] =
                    $this->buildEvent(
                        $session
                    );
            }

            $days[] = [
                'date' =>
                    $day->format(
                        'Y-m-d'
                    ),

                'numero' =>
                    $day->day,

                'semaine' =>
                    $day->isoWeek(),

                'dans_mois' =>
                    $day->month
                    === $reference->month,

                'aujourdhui' =>
                    $day->isToday(),

                'events' =>
                    $events,
            ];

            $cursor->addDay();
        }

        /*
         * Liste :
         * toutes les sessions encore à venir.
         *
         * Une recherche d'un stage permet donc d'afficher
         * toutes ses sessions programmées futures, y compris
         * celles situées dans d'autres mois.
         */
        $listQuery =
            $this
                ->publicSessionsQuery()
                ->where(
                    'fin',
                    '>=',
                    Carbon::today()
                        ->startOfDay()
                );

        $this->applyStageSearch(
            $listQuery,
            $searchTerm
        );

        $listSessions =
            $listQuery
                ->orderBy('debut')
                ->get()
                ->map(
                    fn (
                        SessionStage $session
                    ): array =>
                        $this->buildEvent(
                            $session
                        )
                )
                ->values();

        /*
         * PORTAIL_VUE_SEMAINE_V1
         *
         * Vue hebdomadaire publique : lundi -> vendredi,
         * cohérente avec les autres calendriers de formation.
         * Une session conserve la même ligne sur toute sa durée
         * visible afin d'éviter l'effet "escalier".
         */
        $semaineDemandee =
            $request->query(
                'semaine'
            );

        try {
            $weekReference =
                $semaineDemandee
                    ? Carbon::createFromFormat(
                        'Y-m-d',
                        (string) $semaineDemandee
                    )
                        ->startOfWeek(
                            Carbon::MONDAY
                        )
                    : Carbon::today()
                        ->startOfWeek(
                            Carbon::MONDAY
                        );
        } catch (\Throwable) {
            $weekReference =
                Carbon::today()
                    ->startOfWeek(
                        Carbon::MONDAY
                    );
        }

        $weekStart =
            $weekReference
                ->copy()
                ->startOfDay();

        $weekEnd =
            $weekStart
                ->copy()
                ->addDays(4)
                ->endOfDay();

        $weekQuery =
            $this->publicSessionsQuery();

        $this->applyStageSearch(
            $weekQuery,
            $searchTerm
        );

        $weekSessions =
            $weekQuery
                ->where(
                    'debut',
                    '<=',
                    $weekEnd
                )
                ->where(
                    'fin',
                    '>=',
                    $weekStart
                )
                ->get()
                ->sort(
                    function (
                        SessionStage $a,
                        SessionStage $b
                    ): int {
                        $startComparison =
                            $a->debut
                                ->timestamp
                            <=> $b->debut
                                ->timestamp;

                        if ($startComparison !== 0) {
                            return $startComparison;
                        }

                        /*
                         * À date de début identique, les stages
                         * les plus longs sont placés en premier.
                         */
                        $durationA =
                            $a->fin->timestamp
                            - $a->debut->timestamp;

                        $durationB =
                            $b->fin->timestamp
                            - $b->debut->timestamp;

                        return $durationB <=> $durationA;
                    }
                )
                ->values();

        $weekLaneEnds = [];
        $weekLaneBySession = [];

        foreach ($weekSessions as $session) {
            $visibleStart =
                $session->debut
                    ->copy()
                    ->startOfDay();

            if ($visibleStart->lt($weekStart)) {
                $visibleStart =
                    $weekStart->copy();
            }

            $visibleEnd =
                $session->fin
                    ->copy()
                    ->endOfDay();

            if ($visibleEnd->gt($weekEnd)) {
                $visibleEnd =
                    $weekEnd->copy();
            }

            $lane = 0;

            while (
                isset(
                    $weekLaneEnds[$lane]
                )
                && ! $visibleStart->gt(
                    $weekLaneEnds[$lane]
                )
            ) {
                $lane++;
            }

            $weekLaneEnds[$lane] =
                $visibleEnd;

            $weekLaneBySession[
                $session->id
            ] = $lane;
        }

        $weekLaneCount =
            max(
                1,
                count($weekLaneEnds)
            );

        $weekDays = [];

        for ($i = 0; $i < 5; $i++) {
            $day =
                $weekStart
                    ->copy()
                    ->addDays($i);

            $dayStart =
                $day
                    ->copy()
                    ->startOfDay();

            $dayEnd =
                $day
                    ->copy()
                    ->endOfDay();

            $cells =
                array_fill(
                    0,
                    $weekLaneCount,
                    null
                );

            foreach ($weekSessions as $session) {
                if (
                    ! $session->debut
                        ->lte($dayEnd)
                    || ! $session->fin
                        ->gte($dayStart)
                ) {
                    continue;
                }

                $lane =
                    $weekLaneBySession[
                        $session->id
                    ];

                $cells[$lane] =
                    $this->buildEvent(
                        $session
                    );
            }

            $weekDays[] = [
                'date' =>
                    $day->format('Y-m-d'),

                'jour' =>
                    ucfirst(
                        $day
                            ->locale('fr')
                            ->translatedFormat('l')
                    ),

                'numero' =>
                    $day->day,

                'mois' =>
                    ucfirst(
                        $day
                            ->locale('fr')
                            ->translatedFormat('F')
                    ),

                'aujourdhui' =>
                    $day->isToday(),

                'cells' =>
                    $cells,
            ];
        }

        $weekLabel =
            'Semaine du '
            . $weekStart
                ->locale('fr')
                ->translatedFormat('d F')
            . ' au '
            . $weekEnd
                ->locale('fr')
                ->translatedFormat('d F Y');

        $semainePrecedente =
            $weekStart
                ->copy()
                ->subWeek()
                ->format('Y-m-d');

        $semaineSuivante =
            $weekStart
                ->copy()
                ->addWeek()
                ->format('Y-m-d');

        $semaineActuelle =
            Carbon::today()
                ->startOfWeek(
                    Carbon::MONDAY
                )
                ->format('Y-m-d');

        $semaineCourante =
            $weekStart
                ->format('Y-m-d');
        return view(
            'fpsplanificationstage::public.calendrier',
            [
                'days' =>
                    $days,

                'moisLabel' =>
                    ucfirst(
                        $reference
                            ->locale('fr')
                            ->translatedFormat(
                                'F Y'
                            )
                    ),

                'moisPrecedent' =>
                    $reference
                        ->copy()
                        ->subMonth()
                        ->format('Y-m'),

                'moisSuivant' =>
                    $reference
                        ->copy()
                        ->addMonth()
                        ->format('Y-m'),

                'moisActuel' =>
                    Carbon::today()
                        ->format('Y-m'),

                'moisCourant' =>
                    $reference
                        ->format('Y-m'),

                'weekDays' =>
                    $weekDays,

                'weekLabel' =>
                    $weekLabel,

                'semainePrecedente' =>
                    $semainePrecedente,

                'semaineSuivante' =>
                    $semaineSuivante,

                'semaineActuelle' =>
                    $semaineActuelle,

                'semaineCourante' =>
                    $semaineCourante,
                'viewMode' =>
                    $viewMode,

                'searchTerm' =>
                    $searchTerm,

                'listSessions' =>
                    $listSessions,

                'matchingSessionsCount' =>
                    $searchTerm !== ''
                        ? $listSessions
                            ->count()
                        : null,
            ]
        );
    }

    private function publicSessionsQuery(): Builder
    {
        return SessionStage::query()
            ->with([
                'stage',
                'salle',
            ])
            ->whereIn(
                'statut',
                [
                    'planifiee',
                    'confirmee',
                ]
            );
    }

    private function applyStageSearch(
        Builder $query,
        string $searchTerm
    ): void {
        if ($searchTerm === '') {
            return;
        }

        $like =
            '%'
            . $searchTerm
            . '%';

        $query->whereHas(
            'stage',
            function (
                Builder $stageQuery
            ) use (
                $like
            ): void {
                $stageQuery->where(
                    function (
                        Builder $where
                    ) use (
                        $like
                    ): void {
                        $where
                            ->where(
                                'libelle_court',
                                'like',
                                $like
                            )
                            ->orWhere(
                                'libelle_long',
                                'like',
                                $like
                            )
                            ->orWhere(
                                'lieux_formation',
                                'like',
                                $like
                            )
                            ->orWhere(
                                'centre_formation',
                                'like',
                                $like
                            )
                            ->orWhere(
                                'service_emetteur',
                                'like',
                                $like
                            );
                    }
                );
            }
        );
    }

    private function buildEvent(
        SessionStage $session
    ): array {
        $capacite =
            $session
                ->capacite_max;

        $reservees =
            $session
                ->places_reservees;

        $restantes =
            $session
                ->places_restantes;

        $complete =
            $capacite !== null
            && $restantes !== null
            && $restantes <= 0;

        $stage =
            $session->stage;

        $fps =
            $stage
                ?->lieux_formation
            ?: $stage
                ?->centre_formation;

        return [
            'id' =>
                $session->id,

            'code' =>
                $session
                    ->code_session,

            'stage' =>
                $stage
                    ?->libelle_court
                ?? 'Stage',

            /*
             * Conserve la fonctionnalité installée
             * précédemment : libellé long au survol.
             */
            'stage_long' =>
                $stage
                    ?->libelle_long
                ?: (
                    $stage
                        ?->libelle_court
                    ?? 'Stage'
                ),

            'fps' =>
                $fps,

            'service_responsable' =>
                $stage
                    ?->service_emetteur,

            'debut' =>
                $session
                    ->debut
                    ->format(
                        'd/m/Y H:i'
                    ),

            'fin' =>
                $session
                    ->fin
                    ->format(
                        'd/m/Y H:i'
                    ),

            'debut_date' =>
                $session
                    ->debut
                    ->format(
                        'Y-m-d'
                    ),

            'debut_date_label' =>
                ucfirst(
                    $session
                        ->debut
                        ->locale('fr')
                        ->translatedFormat(
                            'l d F Y'
                        )
                ),

            'salle' =>
                $session
                    ->salle
                    ?->nom,

            'capacite' =>
                $capacite,

            'reservees' =>
                $reservees,

            'restantes' =>
                $restantes,

            'complete' =>
                $complete,

            'inscription_url' =>
                $this->publicRelativeRoute(
                    'fpsplanificationstage.public.inscription.create',
                    [
                        'session' =>
                            $session->id,
                    ]
                ),
        ];
    }
    /*
     * PORTAIL_URLS_RELATIVES_V1
     *
     * Les routes du module portent déjà le préfixe /apps.
     * absolute=false évite de rajouter le /apps présent
     * dans APP_URL.
     */
    private function publicRelativeRoute(
        string $name,
        array $parameters = []
    ): string {
        return route(
            $name,
            $parameters,
            false
        );
    }


    public function show(
        SessionStage $session
    ): View {
        $session->load([
            'stage.prerequis' =>
                fn ($query) =>
                    $query
                        ->where(
                            'actif',
                            true
                        )
                        ->orderBy(
                            'ordre'
                        ),
            'salle',
        ]);

        $stage = $session->stage;

        if (! $stage) {
            abort(404);
        }

        return view(
            'fpsplanificationstage::public.formation-detail',
            [
                'session' => $session,
                'stage' => $stage,

                'inscriptionUrl' =>
                    route(
                        'fpsplanificationstage.public.inscription.create',
                        [
                            'session' => $session->id,
                        ],
                        false
                    ),

                'retourUrl' =>
                    '/apps/fpsplanificationstage/espace-stagiaire/planning-formations',

                'inscriptionPossible' =>
                    ! in_array(
                        $session->statut,
                        [
                            'annulee',
                            'terminee',
                        ],
                        true
                    ),
            ]
        );
    }

}
