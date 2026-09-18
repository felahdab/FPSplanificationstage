<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $stage->libelle_court ?? 'Formation' }}
        — Portail des formations
    </title>

    @php
        $stageHue =
            (
                (int) sprintf(
                    '%u',
                    crc32(
                        (string) (
                            $stage->libelle_court
                            ?? ''
                        )
                    )
                )
            )
            % 360;

        $publicLieu =
            trim(
                (string) (
                    $stage->lieux_formation
                    ?: $stage->centre_formation
                    ?: ''
                )
            );

        $reservees =
            $session
                ->places_reservees;

        $modules =
            $stage
                ->fifModules;

        $evaluations =
            collect([
                'Diagnostique' =>
                    $stage
                        ->evaluation_diagnostique,

                'Formative' =>
                    $stage
                        ->evaluation_formative,

                'Certificative' =>
                    $stage
                        ->evaluation_certificative,

                'Format / modalités' =>
                    $stage
                        ->evaluation_format,
            ])->filter(
                fn ($value) =>
                    filled(
                        $value
                    )
            );

        $pedagogie =
            collect([
                'Travail en groupes' =>
                    $stage
                        ->pedagogie_groupes,

                'Visite' =>
                    $stage
                        ->pedagogie_visite,

                'Vidéo' =>
                    $stage
                        ->pedagogie_video,

                'Tableau interactif' =>
                    $stage
                        ->pedagogie_tableau_interactif,

                'Autre' =>
                    $stage
                        ->pedagogie_autre,
            ])->filter(
                fn ($value) =>
                    filled(
                        $value
                    )
            );

        $hasOldCompetencies =
            filled(
                $stage
                    ->domaines_competences_vises
            )
            || filled(
                $stage
                    ->criteres_certification
            );

        $hasEad =
            (bool)
                $stage
                    ->possibilite_ead
            || filled(
                $stage
                    ->duree_ead_ui
            );
    @endphp

    <style>
        :root {
            color-scheme: light;
            --stage-hue:
                {{ $stageHue }};
            --stage:
                hsl(
                    var(--stage-hue)
                    70%
                    45%
                );
            --stage-dark:
                hsl(
                    var(--stage-hue)
                    72%
                    31%
                );
            --stage-soft:
                hsl(
                    var(--stage-hue)
                    80%
                    96%
                );
            --green: #15803d;
            --red: #c2410c;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-900: #0f172a;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--gray-50);
            color: var(--gray-900);
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .page {
            width:
                min(
                    1120px,
                    calc(
                        100% - 32px
                    )
                );
            margin:
                32px auto 64px;
        }

        .back {
            display: inline-flex;
            margin-bottom: 18px;
            color: var(--stage-dark);
            font-weight: 750;
            text-decoration: none;
        }

        .hero {
            padding: 28px;
            border:
                1px solid
                var(--gray-200);
            border-top:
                6px solid
                var(--stage);
            border-radius: 20px;
            background: var(--white);
            box-shadow:
                0 12px 30px
                rgba(
                    15,
                    23,
                    42,
                    .06
                );
        }

        .eyebrow {
            margin-bottom: 8px;
            color: var(--stage-dark);
            font-size: 14px;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        h1 {
            margin: 0;
            font-size:
                clamp(
                    28px,
                    4vw,
                    44px
                );
            line-height: 1.1;
        }

        .long-title {
            margin: 10px 0 0;
            color: var(--gray-700);
            font-size: 18px;
            line-height: 1.5;
        }

        .session-card {
            margin-top: 22px;
            padding: 18px;
            border-radius: 16px;
            background: var(--stage-soft);
        }

        .session-title {
            margin-bottom: 12px;
            color: var(--stage-dark);
            font-weight: 850;
        }

        .session-grid,
        .capacity-grid,
        .details {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );
            gap: 14px;
        }

        .session-item,
        .capacity-item {
            min-width: 0;
        }

        .label {
            display: block;
            margin-bottom: 4px;
            color: var(--gray-500);
            font-size: 12px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .035em;
        }

        .value {
            font-weight: 800;
            line-height: 1.4;
        }

        .capacity-grid {
            margin-top: 14px;
        }

        .capacity-item {
            padding: 12px 14px;
            border:
                1px solid
                color-mix(
                    in srgb,
                    var(--stage) 20%,
                    white
                );
            border-radius: 12px;
            background: rgba(
                255,
                255,
                255,
                .76
            );
        }

        .ok {
            color: var(--green);
        }

        .full {
            color: var(--red);
        }

        .grid {
            display: grid;
            grid-template-columns:
                repeat(
                    2,
                    minmax(
                        0,
                        1fr
                    )
                );
            gap: 18px;
            margin-top: 18px;
        }

        .card {
            padding: 22px;
            border:
                1px solid
                var(--gray-200);
            border-radius: 18px;
            background: var(--white);
        }

        .card.full-width {
            grid-column:
                1 / -1;
        }

        .card h2 {
            margin: 0 0 18px;
            font-size: 19px;
        }

        .details {
            grid-template-columns:
                repeat(
                    2,
                    minmax(
                        0,
                        1fr
                    )
                );
            gap: 18px 24px;
        }

        .detail.full-width {
            grid-column:
                1 / -1;
        }

        .detail-label {
            display: block;
            margin-bottom: 5px;
            color: var(--gray-500);
            font-size: 13px;
            font-weight: 750;
        }

        .detail-value {
            white-space: pre-line;
            line-height: 1.55;
            font-weight: 600;
        }

        .muted {
            color: var(--gray-500);
            font-weight: 500;
        }

        .prereq-list,
        .simple-list {
            margin: 0;
            padding-left: 22px;
        }

        .prereq-list li,
        .simple-list li {
            margin: 8px 0;
            line-height: 1.45;
        }

        .module {
            padding: 14px 0;
            border-top:
                1px solid
                var(--gray-200);
        }

        .module:first-child {
            padding-top: 0;
            border-top: 0;
        }

        .module-title {
            font-weight: 850;
        }

        .module-objective {
            margin-top: 5px;
            color: var(--gray-700);
            line-height: 1.5;
            white-space: pre-line;
        }

        .cta {
            position: sticky;
            bottom: 16px;
            display: flex;
            align-items: center;
            justify-content:
                space-between;
            gap: 18px;
            margin-top: 22px;
            padding: 18px 20px;
            border:
                1px solid
                var(--gray-200);
            border-radius: 18px;
            background:
                rgba(
                    255,
                    255,
                    255,
                    .96
                );
            box-shadow:
                0 12px 36px
                rgba(
                    15,
                    23,
                    42,
                    .12
                );
            backdrop-filter:
                blur(
                    8px
                );
        }

        .cta-text strong {
            display: block;
            margin-bottom: 4px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 22px;
            border: 0;
            border-radius: 12px;
            background: var(--stage);
            color: var(--white);
            font-size: 15px;
            font-weight: 850;
            text-decoration: none;
            white-space: nowrap;
        }

        .button:hover {
            background:
                var(--stage-dark);
        }

        .button.disabled {
            cursor: not-allowed;
            background: #94a3b8;
        }

        @media (
            max-width: 760px
        ) {
            .page {
                width:
                    min(
                        100% - 20px,
                        1120px
                    );
                margin-top: 18px;
            }

            .hero {
                padding: 20px;
            }

            .session-grid,
            .capacity-grid,
            .grid,
            .details {
                grid-template-columns:
                    1fr;
            }

            .card.full-width,
            .detail.full-width {
                grid-column: auto;
            }

            .cta {
                align-items: stretch;
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <a
            class="back"
            href="{{ $retourUrl }}"
        >
            ← Retour au calendrier
        </a>

        <section class="hero">
            <div class="eyebrow">
                {{
                    $stage->libelle_court
                    ?? 'Formation'
                }}
            </div>

            <h1>
                {{ $intitule }}
            </h1>

            @if (
                filled(
                    $stage->libelle_long
                )
                && trim(
                    (string)
                    $stage->libelle_long
                )
                    !== trim(
                        (string)
                        $intitule
                    )
            )
                <p class="long-title">
                    {{
                        $stage
                            ->libelle_long
                    }}
                </p>
            @endif

            <div class="session-card">
                <div class="session-title">
                    Session sélectionnée
                </div>

                <div class="session-grid">
                    <div class="session-item">
                        <span class="label">
                            Début
                        </span>
                        <div class="value">
                            {{
                                $session
                                    ->debut
                                    ->format(
                                        'd/m/Y H:i'
                                    )
                            }}
                        </div>
                    </div>

                    <div class="session-item">
                        <span class="label">
                            Fin
                        </span>
                        <div class="value">
                            {{
                                $session
                                    ->fin
                                    ->format(
                                        'd/m/Y H:i'
                                    )
                            }}
                        </div>
                    </div>

                    <div class="session-item">
                        <span class="label">
                            Durée
                        </span>
                        <div class="value">
                            {{ $duree }}
                        </div>
                    </div>
                </div>

                <div class="capacity-grid">
                    <div class="capacity-item">
                        <span class="label">
                            Capacité
                        </span>
                        <div class="value">
                            {{
                                $capacite
                                ?? 'Non renseignée'
                            }}
                        </div>
                    </div>

                    <div class="capacity-item">
                        <span class="label">
                            Candidatures occupant une place
                        </span>
                        <div class="value">
                            {{ $reservees }}
                        </div>
                    </div>

                    <div class="capacity-item">
                        <span class="label">
                            Places disponibles
                        </span>

                        @if ($complete)
                            <div class="value full">
                                Session complète
                            </div>
                        @elseif (
                            $restantes
                            !== null
                        )
                            <div class="value ok">
                                {{ $restantes }}
                            </div>
                        @else
                            <div class="value">
                                Non renseigné
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="grid">
            <section class="card">
                <h2>
                    La formation
                </h2>

                <div class="details">
                    <div class="detail full-width">
                        <span class="detail-label">
                            Intitulé de la formation
                        </span>
                        <div class="detail-value">
                            {{ $intitule }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Service émetteur
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $stage
                                        ->service_emetteur
                                )
                                    ? $stage
                                        ->service_emetteur
                                    : 'Non renseigné'
                            }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Typologie de la formation
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $stage
                                        ->typologie
                                )
                                    ? $stage
                                        ->typologie
                                    : 'Non renseignée'
                            }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Population concernée
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $population
                                )
                                    ? $population
                                    : 'Non renseignée'
                            }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Niveau
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $stage
                                        ->niveau_brevet
                                )
                                    ? $stage
                                        ->niveau_brevet
                                    : 'Non renseigné'
                            }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Durée
                        </span>
                        <div class="detail-value">
                            {{ $duree }}
                        </div>
                    </div>

                    <div class="detail">
                        <span class="detail-label">
                            Lieu de formation
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $publicLieu
                                )
                                    ? $publicLieu
                                    : 'Non renseigné'
                            }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="card">
                <h2>
                    Finalité
                </h2>

                <div class="details">
                    <div class="detail full-width">
                        <span class="detail-label">
                            Fonction visée par la formation
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $stage
                                        ->fonctions_visees
                                )
                                    ? $stage
                                        ->fonctions_visees
                                    : 'Non renseignée'
                            }}
                        </div>
                    </div>

                    <div class="detail full-width">
                        <span class="detail-label">
                            Objectif de la formation
                        </span>
                        <div class="detail-value">
                            {{
                                filled(
                                    $stage
                                        ->objectif_formation
                                )
                                    ? $stage
                                        ->objectif_formation
                                    : 'Non renseigné'
                            }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="card full-width">
                <h2>
                    Pré-requis
                </h2>

                @if (
                    $stage
                        ->prerequis
                        ->isNotEmpty()
                )
                    <ul class="prereq-list">
                        @foreach (
                            $stage->prerequis
                            as $prerequis
                        )
                            <li>
                                {{
                                    $prerequis
                                        ->libelle
                                }}

                                @if (
                                    $prerequis
                                        ->obligatoire
                                )
                                    <strong>
                                        — obligatoire
                                    </strong>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="muted">
                        Aucun pré-requis renseigné.
                    </div>
                @endif
            </section>

            @if (
                $modules
                    ->isNotEmpty()
            )
                <section class="card full-width">
                    <h2>
                        Modules et compétences
                    </h2>

                    @foreach (
                        $modules
                        as $module
                    )
                        <div class="module">
                            <div class="module-title">
                                {{
                                    $module
                                        ->module
                                }}
                            </div>

                            @if (
                                filled(
                                    $module
                                        ->objectifs_competences
                                )
                            )
                                <div class="module-objective">
                                    {{
                                        $module
                                            ->objectifs_competences
                                    }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </section>
            @endif

            @if (
                $hasOldCompetencies
            )
                <section class="card full-width">
                    <h2>
                        Compétences et certification
                    </h2>

                    <div class="details">
                        @if (
                            filled(
                                $stage
                                    ->domaines_competences_vises
                            )
                        )
                            <div class="detail">
                                <span class="detail-label">
                                    Domaines de compétences visés
                                </span>
                                <div class="detail-value">
                                    {{
                                        $stage
                                            ->domaines_competences_vises
                                    }}
                                </div>
                            </div>
                        @endif

                        @if (
                            filled(
                                $stage
                                    ->criteres_certification
                            )
                        )
                            <div class="detail">
                                <span class="detail-label">
                                    Critères de certification
                                </span>
                                <div class="detail-value">
                                    {{
                                        $stage
                                            ->criteres_certification
                                    }}
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            @if (
                $evaluations
                    ->isNotEmpty()
            )
                <section class="card">
                    <h2>
                        Évaluations
                    </h2>

                    <ul class="simple-list">
                        @foreach (
                            $evaluations
                            as $label =>
                                $value
                        )
                            <li>
                                <strong>
                                    {{ $label }} :
                                </strong>
                                {{ $value }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if (
                $pedagogie
                    ->isNotEmpty()
                || $hasEad
            )
                <section class="card">
                    <h2>
                        Modalités pédagogiques
                    </h2>

                    @if (
                        $pedagogie
                            ->isNotEmpty()
                    )
                        <ul class="simple-list">
                            @foreach (
                                $pedagogie
                                as $label =>
                                    $value
                            )
                                <li>
                                    <strong>
                                        {{ $label }} :
                                    </strong>
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($hasEad)
                        <div
                            class="detail-value"
                            style="margin-top:12px"
                        >
                            <strong>
                                Formation à distance :
                            </strong>
                            {{
                                $stage
                                    ->possibilite_ead
                                    ? 'possible'
                                    : 'non indiquée'
                            }}

                            @if (
                                filled(
                                    $stage
                                        ->duree_ead_ui
                                )
                            )
                                — durée :
                                {{
                                    $stage
                                        ->duree_ead_ui
                                }}
                            @endif
                        </div>
                    @endif
                </section>
            @endif
        </div>

        <section class="cta">
            <div class="cta-text">
                <strong>
                    Vous souhaitez participer à cette session ?
                </strong>

                @if ($complete)
                    <span class="full">
                        La session est complète.
                        L’inscription reste possible en liste d’attente.
                    </span>
                @else
                    <span class="muted">
                        {{
                            $restantes
                            !== null
                                ? $restantes
                                    . ' place(s) encore disponible(s).'
                                : 'Les candidatures sont ouvertes.'
                        }}
                    </span>
                @endif
            </div>

            <a
                class="button"
                href="{{ $inscriptionUrl }}"
            >
                @if ($complete)
                    M’inscrire en liste d’attente
                @else
                    Je souhaite m’inscrire à cette session
                @endif
            </a>
        </section>
    </main>
</body>
</html>

