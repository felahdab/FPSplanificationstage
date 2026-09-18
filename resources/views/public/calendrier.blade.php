<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Portail des formations
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 2rem 1rem;
            background: #f1f5f9;
            color: #0f172a;
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .container {
            max-width: 1450px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 1.5rem;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }

        h1 {
            margin: 0 0 .4rem;
            font-size: 2rem;
        }

        .subtitle {
            color: #64748b;
        }

        .header-actions {
            display: flex;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .need-button,
        .follow-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            border-radius: .65rem;
            color: white;
            text-decoration: none;
            font-weight: 750;
        }

        .need-button {
            background: #0f766e;
        }

        .need-button:hover {
            background: #115e59;
        }

        .follow-button {
            background: #475569;
        }

        .follow-button:hover {
            background: #334155;
        }

        .flash-success {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #86efac;
            border-radius: .8rem;
            background: #f0fdf4;
            color: #166534;
        }

        .flash-icon {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1;
        }

        .flash-title {
            font-weight: 800;
            margin-bottom: .2rem;
        }

        .flash-reference {
            font-size: .9rem;
        }

        .portal-info {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .portal-card {
            padding: 1rem 1.2rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .8rem;
        }

        .portal-card strong {
            display: block;
            margin-bottom: .3rem;
        }

        .portal-card span {
            color: #64748b;
            font-size: .9rem;
            line-height: 1.45;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .toolbar-group {
            display: flex;
            gap: .5rem;
            align-items: center;
        }

        .month-title {
            min-width: 220px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 750;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .6rem .9rem;
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: white;
            color: #0f172a;
            font-weight: 650;
            text-decoration: none;
        }

        .button:hover {
            background: #f8fafc;
        }

        .calendar-scroll {
            overflow-x: auto;
        }

        .calendar {
            min-width: 1150px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .9rem;
            overflow: hidden;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .04);
        }

        .week-header {
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .week-header div {
            padding: .75rem;
            text-align: center;
            font-weight: 750;
            color: #475569;
        }

        .week-row {
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
        }

        .week-number {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-weight: 750;
        }

        .week-number span {
            padding: .35rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .45rem;
        }

        .day {
            min-height: 180px;
            padding: .5rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }

        .week-row .day:last-child {
            border-right: none;
        }

        .day.outside {
            background: #f8fafc;
        }

        .day-number {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            font-weight: 750;
            margin-bottom: .5rem;
        }

        .outside .day-number {
            color: #94a3b8;
        }

        .day-number.today {
            background: #2563eb;
            color: white;
        }

        .session {
            padding: .65rem;
            margin-bottom: .5rem;
            border-radius: .6rem;
            background:
                var(
                    --stage-bg,
                    #eff6ff
                );
            border-left:
                4px solid
                var(
                    --stage-border,
                    #2563eb
                );
        }

        .session-title {
            margin-bottom: .25rem;
            font-weight: 800;
            color:
                var(
                    --stage-text,
                    #1e3a8a
                );
        }

        .places {
            margin-top: .35rem;
            font-size: .78rem;
            font-weight: 700;
        }

        .places.available {
            color: #15803d;
        }

        .places.full {
            color: #c2410c;
        }

        .register {
            display: inline-flex;
            margin-top: .5rem;
            padding: .42rem .65rem;
            border-radius: .5rem;
            background:
                var(
                    --stage-border,
                    #2563eb
                );
            color: white;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 750;
        }

        .register:hover {
            background:
                var(
                    --stage-button-hover,
                    #1d4ed8
                );
        }

        .empty {
            margin-top: .5rem;
            font-size: .75rem;
            color: #94a3b8;
        }

        .legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            color: #64748b;
            font-size: .85rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .legend-dot {
            width: .8rem;
            height: .8rem;
            border-radius: 9999px;
        }

        .dot-stage {
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #16a34a,
                    #9333ea,
                    #ea580c
                );
        }

        .dot-full {
            background: #f59e0b;
        }

        @media (max-width: 900px) {
            .portal-info {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            body {
                padding: 1rem .6rem;
            }

            h1 {
                font-size: 1.55rem;
            }

            .header-actions {
                width: 100%;
            }

            .need-button,
            .follow-button {
                flex: 1;
            }
        }
    
        /* PDF_CANDIDATURE_POPUP_V1 */
        .candidature-pdf-box {
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-wrap: wrap;
            margin-top: .9rem;
            padding: .8rem;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
        }

        .candidature-pdf-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.7rem;
            height: 2.2rem;
            padding: 0 .4rem;
            border-radius: .4rem;
            background: #dc2626;
            color: white;
            font-size: .72rem;
            font-weight: 800;
        }

        .candidature-pdf-content {
            display: flex;
            flex: 1 1 220px;
            flex-direction: column;
            gap: .15rem;
        }

        .candidature-pdf-content span {
            color: #475569;
            font-size: .85rem;
        }

        .candidature-pdf-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .62rem .85rem;
            border-radius: .55rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: .86rem;
            font-weight: 750;
        }

        .candidature-pdf-button:hover {
            background: #1d4ed8;
        }

        /* PORTAIL_LIBELLE_LONG_SURVOL_V1_1 */
        .session {
            position: relative;
        }

        .session:hover {
            z-index: 50;
        }

        .stage-long-tooltip {
            display: none;
            position: absolute;
            left: 0;
            top: calc(100% + .45rem);
            z-index: 1000;
            min-width: 230px;
            width: max-content;
            max-width: 380px;
            padding: .7rem .8rem;
            border-radius: .6rem;
            background: #0f172a;
            color: white;
            box-shadow:
                0 10px 25px
                rgba(15, 23, 42, .22);
            font-size: .82rem;
            font-weight: 600;
            line-height: 1.4;
            white-space: normal;
            pointer-events: none;
        }

        .session:hover .stage-long-tooltip {
            display: block;
        }

        .stage-long-tooltip::before {
            content: "";
            position: absolute;
            left: 1rem;
            bottom: 100%;
            border-left: .35rem solid transparent;
            border-right: .35rem solid transparent;
            border-bottom: .35rem solid #0f172a;
        }

        @media (max-width: 700px) {
            .stage-long-tooltip {
                max-width: 280px;
            }
        }

        /* PORTAIL_VUES_RECHERCHE_STAGES_V1_3 */
        .portal-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .stage-search {
            display: flex;
            flex: 1 1 560px;
            align-items: center;
            gap: .55rem;
        }

        .stage-search input[type="search"] {
            flex: 1 1 auto;
            min-width: 210px;
            padding: .72rem .85rem;
            border: 1px solid #cbd5e1;
            border-radius: .65rem;
            background: white;
            color: #0f172a;
            font: inherit;
        }

        .stage-search input[type="search"]:focus {
            border-color: #3b82f6;
            outline: 2px solid #bfdbfe;
            outline-offset: 1px;
        }

        .search-button,
        .clear-search,
        .view-switch {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            min-height: 42px;
            padding: .65rem .85rem;
            border: 1px solid #cbd5e1;
            border-radius: .65rem;
            background: white;
            color: #334155;
            text-decoration: none;
            font-weight: 750;
            cursor: pointer;
        }

        .search-button {
            border-color: #2563eb;
            background: #2563eb;
            color: white;
        }

        .search-button:hover {
            background: #1d4ed8;
        }

        .clear-search:hover,
        .view-switch:hover {
            background: #f8fafc;
        }

        .view-switcher {
            display: inline-flex;
            gap: .4rem;
        }

        .view-switch.active {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .view-switch svg {
            width: 19px;
            height: 19px;
            flex: 0 0 auto;
        }

        .search-summary {
            flex-basis: 100%;
            padding: .7rem .85rem;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: .9rem;
        }

        .search-summary a {
            margin-left: .35rem;
            color: #1d4ed8;
            font-weight: 800;
        }

        .calendar-view-hidden {
            display: none !important;
        }

        .list-view-container {
            max-width: 1450px;
            margin: 1rem auto 0;
        }

        .sessions-list {
            display: flex;
            flex-direction: column;
            gap: 1.35rem;
        }

        .sessions-date-group {
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .sessions-date-title {
            padding-bottom: .45rem;
            border-bottom: 1px solid #cbd5e1;
            color: #334155;
            font-size: 1.02rem;
            font-weight: 800;
        }

        .list-session-card {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr) auto;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            border-radius: .75rem;
            background: white;
        }

        .list-session-card.full {
            border-left-color: #f59e0b;
        }

        .list-session-title {
            color: #1e3a8a;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .list-session-long {
            margin-top: .25rem;
            color: #475569;
            font-size: .9rem;
            line-height: 1.4;
        }

        .list-session-meta {
            display: flex;
            gap: .45rem 1rem;
            flex-wrap: wrap;
            margin-top: .65rem;
            color: #475569;
            font-size: .83rem;
        }

        .list-session-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: .55rem;
            min-width: 180px;
        }

        .list-places {
            font-size: .85rem;
            font-weight: 800;
        }

        .list-places.available {
            color: #15803d;
        }

        .list-places.full {
            color: #c2410c;
        }

        .list-empty {
            padding: 2rem 1rem;
            border: 1px dashed #cbd5e1;
            border-radius: .75rem;
            background: white;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 760px) {
            .stage-search {
                flex-basis: 100%;
                flex-wrap: wrap;
            }

            .stage-search input[type="search"] {
                flex-basis: 100%;
            }

            .view-switcher {
                width: 100%;
            }

            .view-switch {
                flex: 1;
            }

            .list-session-card {
                grid-template-columns: 1fr;
            }

            .list-session-actions {
                align-items: stretch;
                min-width: 0;
            }
        }

        /* CALENDRIER_ALIGNEMENT_STAGES_V1 */
        .session-slot {
            margin-bottom: .5rem;
        }

        .session-slot > .session {
            height: 100%;
            margin-bottom: 0;
        }

        .session-slot-empty {
            visibility: hidden;
            pointer-events: none;
        }

        /* PORTAIL_VUE_SEMAINE_V1 */
        .week-view-container {
            max-width: 1450px;
            margin: 1rem auto 0;
        }

        .week-toolbar {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: .8rem;
            margin-bottom: 1rem;
        }

        .week-toolbar-center {
            text-align: center;
        }

        .week-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: #0f172a;
        }

        .week-toolbar-actions {
            display: flex;
            gap: .45rem;
            align-items: center;
        }

        .week-calendar-scroll {
            overflow-x: auto;
        }

        .week-calendar {
            min-width: 1000px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: .9rem;
            background: white;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }

        .week-days-header,
        .week-events-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .week-day-header {
            padding: .8rem .7rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
        }

        .week-day-header:last-child {
            border-right: 0;
        }

        .week-day-name {
            display: block;
            color: #475569;
            font-size: .82rem;
            font-weight: 750;
            text-transform: capitalize;
        }

        .week-day-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.1rem;
            height: 2.1rem;
            margin-top: .25rem;
            padding: 0 .4rem;
            border-radius: 9999px;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 850;
        }

        .week-day-number.today {
            background: #2563eb;
            color: white;
        }

        .week-cell {
            min-height: 118px;
            padding: .5rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }

        .week-events-grid .week-cell:nth-child(5n) {
            border-right: 0;
        }

        .week-cell .session {
            height: 100%;
            min-height: 96px;
            margin-bottom: 0;
        }

        .week-empty-lane {
            min-height: 96px;
        }

        .week-no-session {
            grid-column: 1 / -1;
            padding: 2rem 1rem;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 760px) {
            .week-toolbar {
                grid-template-columns: 1fr;
            }

            .week-toolbar-center {
                order: -1;
            }

            .week-toolbar-actions {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
</style>
</head>

<body>
{{-- PORTAIL_URLS_RELATIVES_V1 --}}
@php
    $publicRoute =
        static fn (
            string $name,
            array $parameters = []
        ): string =>
            route(
                $name,
                $parameters,
                false
            );
@endphp


<div class="container">

    <div class="header">

        <div class="header-top">

            <div>

                <h1>
                    Portail des formations
                </h1>

                <div class="subtitle">
                    Consultez les sessions,
                    inscrivez-vous ou transmettez
                    un besoin de formation.
                </div>

            </div>

            <div class="header-actions">

                <a
                    class="need-button"
                    href="{{ $publicRoute(
                        'planificationstages.public.besoin.create'
                    ) }}"
                >
                    Exprimer un besoin en stage
                </a>

                <a
                    class="follow-button"
                    href="{{ $publicRoute(
                        'planificationstages.public.besoin.suivi.form'
                    ) }}"
                >
                    Suivre un besoin
                </a>

            </div>

        </div>

    </div>

    @if (session('inscription_success'))

        <div class="flash-success">

            <div class="flash-icon">
                ✓
            </div>

            <div>

                <div class="flash-title">
                    {{ session('inscription_success') }}
                </div>

                @if (session('inscription_code'))

                    <div class="flash-reference">
                        Référence :
                        <strong>
                            {{ session('inscription_code') }}
                        </strong>
                    </div>

                
                    {{-- PDF_CANDIDATURE_POPUP_V1 --}}
                    @if (session('inscription_pdf_url'))

                        <div class="candidature-pdf-box">

                            <div class="candidature-pdf-icon">
                                PDF
                            </div>

                            <div class="candidature-pdf-content">

                                <strong>
                                    Fiche de candidature
                                </strong>

                                <span>
                                    Conservez le PDF récapitulatif de votre candidature.
                                </span>

                            </div>

                            <a
                                class="candidature-pdf-button"
                                href="{{ session('inscription_pdf_url') }}"
                            >
                                Télécharger le PDF
                            </a>

                        </div>

                    @endif
@endif

            </div>

        </div>

    @endif

    <div class="portal-info">

        <div class="portal-card">

            <strong>
                Vous souhaitez vous inscrire ?
            </strong>

            <span>
                Choisissez directement une session
                disponible dans le calendrier ci-dessous.
            </span>

        </div>

        <div class="portal-card">

            <strong>
                Aucune session ne correspond à votre besoin ?
            </strong>

            <span>
                Votre bâtiment ou votre unité peut transmettre
                directement une expression de besoin.
            </span>

        </div>

        <div class="portal-card">

            <strong>
                Vous avez déjà exprimé un besoin ?
            </strong>

            <span>
                Utilisez votre référence BES-xxxxxx
                et votre adresse e-mail pour suivre
                son avancement.
            </span>

        </div>

    </div>

        {{-- PORTAIL_VUES_RECHERCHE_STAGES_V1_3 --}}
    <div class="portal-tools">

        <form
            class="stage-search"
            method="GET"
            action="{{ $publicRoute(
                'planificationstages.public.calendrier'
            ) }}"
        >
            <input
                type="hidden"
                name="vue"
                value="{{ $viewMode }}"
            >

            @if ($viewMode === 'calendrier')
                <input
                    type="hidden"
                    name="mois"
                    value="{{ $moisCourant }}"
                >
            @endif

            @if ($viewMode === 'semaine')
                <input
                    type="hidden"
                    name="semaine"
                    value="{{ $semaineCourante }}"
                >
            @endif

            <input
                type="search"
                name="q"
                value="{{ $searchTerm }}"
                placeholder="Rechercher : libellé, FPS, service responsable…"
                aria-label="Rechercher un stage"
            >

            <button
                class="search-button"
                type="submit"
            >
                Rechercher
            </button>

            @if ($searchTerm !== '')
                <a
                    class="clear-search"
                    href="{{ $publicRoute(
                        'planificationstages.public.calendrier',
                        array_filter([
                            'vue' => $viewMode,
                            'mois' =>
                                $viewMode === 'calendrier'
                                    ? $moisCourant
                                    : null,
                        ])
                    ) }}"
                >
                    Effacer
                </a>
            @endif
        </form>

        <div
            class="view-switcher"
            aria-label="Choisir la vue"
        >
            <a
                class="
                    view-switch
                    {{ $viewMode === 'calendrier' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'vue' => 'calendrier',
                        'mois' => $moisCourant,
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue calendrier"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
                Calendrier
            </a>

                        {{-- PORTAIL_VUE_SEMAINE_V1 --}}
            <a
                class="
                    view-switch
                    {{ $viewMode === 'semaine' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'vue' => 'semaine',
                        'semaine' => $semaineCourante,
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue semaine"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                    <path d="M7 14h2M11 14h2M15 14h2M7 18h2M11 18h2" />
                </svg>
                Semaine
            </a>
<a
                class="
                    view-switch
                    {{ $viewMode === 'liste' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'vue' => 'liste',
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue liste"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="M8 6h13M8 12h13M8 18h13" />
                    <path d="M3 6h.01M3 12h.01M3 18h.01" />
                </svg>
                Liste
            </a>
        </div>

        @if ($searchTerm !== '')
            <div class="search-summary">
                Recherche :
                <strong>
                    « {{ $searchTerm }} »
                </strong>
                —
                {{ $matchingSessionsCount }}
                session(s) programmée(s) à venir.

                @if (
                    $viewMode === 'calendrier'
                    && $matchingSessionsCount > 0
                )
                    <a
                        href="{{ $publicRoute(
                            'planificationstages.public.calendrier',
                            [
                                'vue' => 'liste',
                                'q' => $searchTerm,
                            ]
                        ) }}"
                    >
                        Afficher toutes les sessions
                    </a>
                @endif
            </div>
        @endif

    </div>
<div class="toolbar {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="toolbar-group">

            <a
                class="button"
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'mois' => $moisPrecedent,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                ←
            </a>

        </div>

        <div class="month-title">
            {{ $moisLabel }}
        </div>

        <div class="toolbar-group">

            <a
                class="button"
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'mois' => $moisActuel,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                Aujourd’hui
            </a>

            <a
                class="button"
                href="{{ $publicRoute(
                    'planificationstages.public.calendrier',
                    array_filter([
                        'mois' => $moisSuivant,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                →
            </a>

        </div>

    </div>

    <div class="calendar-scroll {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="calendar">

            <div class="week-header">

                <div>Sem.</div>

                <div>Lun</div>
                <div>Mar</div>
                <div>Mer</div>
                <div>Jeu</div>
                <div>Ven</div>

            </div>

            @foreach (
                array_chunk(
                    array_values(
                        array_filter(
                            $days,
                            fn ($day) =>
                                \Carbon\Carbon::parse(
                                    $day['date']
                                )->isWeekday()
                        )
                    ),
                    5
                )
                as $week
            )

                
                {{-- CALENDRIER_ALIGNEMENT_STAGES_V1 --}}
                @php
                    /*
                     * Une session garde une ligne fixe pendant toute
                     * la semaine. Les sessions les plus longues sont
                     * placées en premier afin d'éviter l'effet escalier.
                     */
                    $calendarWeekSessions = [];

                    foreach ($week as $calendarDayIndex => $calendarDay) {
                        foreach (($calendarDay['events'] ?? []) as $calendarEvent) {
                            $calendarEventKey = (string) (
                                $calendarEvent['id']
                                ?? $calendarEvent['code']
                                ?? md5(json_encode($calendarEvent))
                            );

                            if (! isset($calendarWeekSessions[$calendarEventKey])) {
                                $calendarWeekSessions[$calendarEventKey] = [
                                    'event' => $calendarEvent,
                                    'first_day' => $calendarDayIndex,
                                    'last_day' => $calendarDayIndex,
                                    'days' => [],
                                    'stage' => (string) ($calendarEvent['stage'] ?? ''),
                                ];
                            }

                            $calendarWeekSessions[$calendarEventKey]['first_day'] = min(
                                $calendarWeekSessions[$calendarEventKey]['first_day'],
                                $calendarDayIndex
                            );

                            $calendarWeekSessions[$calendarEventKey]['last_day'] = max(
                                $calendarWeekSessions[$calendarEventKey]['last_day'],
                                $calendarDayIndex
                            );

                            $calendarWeekSessions[$calendarEventKey]['days'][$calendarDayIndex] = true;
                        }
                    }

                    uasort(
                        $calendarWeekSessions,
                        static function (array $left, array $right): int {
                            $leftDuration = count($left['days']);
                            $rightDuration = count($right['days']);

                            if ($leftDuration !== $rightDuration) {
                                return $rightDuration <=> $leftDuration;
                            }

                            if ($left['first_day'] !== $right['first_day']) {
                                return $left['first_day'] <=> $right['first_day'];
                            }

                            $stageCompare = strcasecmp(
                                $left['stage'],
                                $right['stage']
                            );

                            if ($stageCompare !== 0) {
                                return $stageCompare;
                            }

                            return 0;
                        }
                    );

                    $calendarLaneDays = [];
                    $calendarEventLanes = [];

                    foreach ($calendarWeekSessions as $calendarEventKey => $calendarMeta) {
                        $calendarLane = 0;

                        while (true) {
                            $calendarConflict = false;

                            foreach (array_keys($calendarMeta['days']) as $calendarOccupiedDay) {
                                if (isset($calendarLaneDays[$calendarLane][$calendarOccupiedDay])) {
                                    $calendarConflict = true;
                                    break;
                                }
                            }

                            if (! $calendarConflict) {
                                break;
                            }

                            $calendarLane++;
                        }

                        $calendarEventLanes[$calendarEventKey] = $calendarLane;

                        foreach (array_keys($calendarMeta['days']) as $calendarOccupiedDay) {
                            $calendarLaneDays[$calendarLane][$calendarOccupiedDay] = true;
                        }
                    }

                    $calendarWeekLaneCount = count($calendarLaneDays);
                    $calendarWeekSlots = array_fill(0, count($week), []);

                    foreach ($week as $calendarDayIndex => $calendarDay) {
                        foreach (($calendarDay['events'] ?? []) as $calendarEvent) {
                            $calendarEventKey = (string) (
                                $calendarEvent['id']
                                ?? $calendarEvent['code']
                                ?? md5(json_encode($calendarEvent))
                            );

                            if (! array_key_exists($calendarEventKey, $calendarEventLanes)) {
                                continue;
                            }

                            $calendarLane = $calendarEventLanes[$calendarEventKey];
                            $calendarWeekSlots[$calendarDayIndex][$calendarLane] = $calendarEvent;
                        }
                    }
                @endphp
<div class="week-row">

                    <div class="week-number">

                        <span>
                            S{{ $week[0]['semaine'] }}
                        </span>

                    </div>

                    @foreach ($week as $calendarDayIndex => $day)

                        <div
                            class="
                                day
                                {{ $day['dans_mois'] ? '' : 'outside' }}
                            "
                        >

                            <div
                                class="
                                    day-number
                                    {{ $day['aujourdhui'] ? 'today' : '' }}
                                "
                            >
                                {{ $day['numero'] }}
                            </div>

                            
                            @for (
                                $calendarLane = 0;
                                $calendarLane < $calendarWeekLaneCount;
                                $calendarLane++
                            )
                                @php
                                    $event = $calendarWeekSlots[$calendarDayIndex][$calendarLane] ?? null;
                                @endphp

                                @if ($event !== null)
                                    <div
                                        class="session-slot"
                                        data-calendar-lane="{{ $calendarLane }}"
                                    >

                                @php
                                    /*
                                     * Couleur stable par stage.
                                     *
                                     * Le libellé court génère toujours
                                     * la même teinte, sans avoir besoin
                                     * d'enregistrer une couleur en base.
                                     */
                                    $stageHue =
                                        (
                                            (int) sprintf(
                                                '%u',
                                                crc32(
                                                    (string) (
                                                        $event['stage']
                                                        ?? ''
                                                    )
                                                )
                                            )
                                        )
                                        % 360;
                                @endphp

                                <div
                                    class="
                                        session
                                        {{ $event['complete'] ? 'full' : '' }}
                                    "
                                    style="
                                        --stage-bg:
                                            hsl(
                                                {{ $stageHue }}
                                                80%
                                                95%
                                            );
                                        --stage-border:
                                            hsl(
                                                {{ $stageHue }}
                                                70%
                                                45%
                                            );
                                        --stage-text:
                                            hsl(
                                                {{ $stageHue }}
                                                72%
                                                27%
                                            );
                                        --stage-button-hover:
                                            hsl(
                                                {{ $stageHue }}
                                                72%
                                                37%
                                            );
                                    "
                                >

                                    <div class="session-title">
                                        {{ $event['stage'] }}
                                    </div>

                                    {{-- PORTAIL_LIBELLE_LONG_SURVOL_V1_1 --}}
                                    <div class="stage-long-tooltip">
                                        {{ $event['stage_long'] }}
                                    </div>

                                    @if (
                                        $event['capacite']
                                        !== null
                                    )

                                        @if (
                                            $event['complete']
                                        )

                                            <div class="places full">
                                                Session complète
                                            </div>

                                        @else

                                            <div class="places available">
                                                {{ $event['restantes'] }}
                                                place(s) disponible(s)
                                            </div>

                                        @endif

                                    @endif

                                    {{-- INSCRIPTION_NOUVEL_ONGLET_V1 --}}
                                    <a
                                        href="{{ $event['inscription_url'] }}"
                                        class="register"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >

                                        @if ($event['complete'])
                                            Voir / liste d’attente
                                        @else
                                            Voir / s’inscrire
                                        @endif

                                    </a>

                                </div>

                            
                                    </div>
                                @else
                                    <div
                                        class="session-slot session-slot-empty"
                                        data-calendar-lane="{{ $calendarLane }}"
                                        aria-hidden="true"
                                    ></div>
                                @endif
                            @endfor

                        </div>

                    @endforeach

                </div>

            @endforeach

        </div>

    </div>

    <div class="legend {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="legend-item">

            <span
                class="legend-dot dot-stage"
            ></span>

            Une couleur différente identifie chaque stage.

        </div>

        <div class="legend-item">

            <span
                class="legend-dot dot-full"
            ></span>

            Le texte orange signale une session complète.

        </div>

    </div>

</div>


@if ($viewMode === 'liste')

    <div class="container list-view-container">

        @php
            $sessionsParDate =
                $listSessions
                    ->groupBy(
                        'debut_date'
                    );
        @endphp

        <div class="sessions-list">

            @forelse (
                $sessionsParDate
                as $date =>
                    $sessionsDate
            )

                <section class="sessions-date-group">

                    <div class="sessions-date-title">
                        {{
                            $sessionsDate
                                ->first()[
                                    'debut_date_label'
                                ]
                        }}
                    </div>

                    @foreach (
                        $sessionsDate
                        as $event
                    )

                        <article
                            class="
                                list-session-card
                                {{ $event['complete'] ? 'full' : '' }}
                            "
                        >
                            <div>

                                <div class="list-session-title">
                                    {{ $event['stage'] }}
                                </div>

                                @if (
                                    $event['stage_long']
                                    && $event['stage_long']
                                        !== $event['stage']
                                )
                                    <div class="list-session-long">
                                        {{ $event['stage_long'] }}
                                    </div>
                                @endif

                                <div class="list-session-meta">

                                    <span>
                                        <strong>Session :</strong>
                                        {{ $event['code'] }}
                                    </span>

                                    <span>
                                        <strong>Du :</strong>
                                        {{ $event['debut'] }}
                                    </span>

                                    <span>
                                        <strong>Au :</strong>
                                        {{ $event['fin'] }}
                                    </span>

                                    @if ($event['salle'])
                                        <span>
                                            <strong>Salle :</strong>
                                            {{ $event['salle'] }}
                                        </span>
                                    @endif

                                    @if ($event['fps'])
                                        <span>
                                            <strong>FPS :</strong>
                                            {{ $event['fps'] }}
                                        </span>
                                    @endif

                                    @if (
                                        $event[
                                            'service_responsable'
                                        ]
                                    )
                                        <span>
                                            <strong>
                                                Service responsable :
                                            </strong>
                                            {{
                                                $event[
                                                    'service_responsable'
                                                ]
                                            }}
                                        </span>
                                    @endif

                                </div>

                            </div>

                            <div class="list-session-actions">

                                @if (
                                    $event['capacite']
                                    !== null
                                )
                                    @if ($event['complete'])
                                        <div class="list-places full">
                                            Session complète
                                        </div>
                                    @else
                                        <div class="list-places available">
                                            {{ $event['restantes'] }}
                                            place(s) disponible(s)
                                        </div>
                                    @endif
                                @endif

                                <a
                                    href="{{ $event['inscription_url'] }}"
                                    class="register"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    @if ($event['complete'])
                                        S’inscrire en liste d’attente
                                    @else
                                        S’inscrire
                                    @endif
                                </a>

                            </div>

                        </article>

                    @endforeach

                </section>

            @empty

                <div class="list-empty">
                    @if ($searchTerm !== '')
                        Aucune session programmée
                        ne correspond à
                        « {{ $searchTerm }} ».
                    @else
                        Aucune session à venir.
                    @endif
                </div>

            @endforelse

        </div>

    </div>

@endif

<script>
/* CALENDRIER_ALIGNEMENT_STAGES_V1_HEIGHTS */
(() => {
    const alignCalendarLanes = () => {
        document.querySelectorAll('.week-row').forEach((week) => {
            const slots = Array.from(
                week.querySelectorAll('.session-slot[data-calendar-lane]')
            );

            slots.forEach((slot) => {
                slot.style.height = '';
            });

            const lanes = new Set(
                slots.map((slot) => slot.dataset.calendarLane)
            );

            lanes.forEach((lane) => {
                const laneSlots = slots.filter(
                    (slot) => slot.dataset.calendarLane === lane
                );

                let maxHeight = 0;

                laneSlots.forEach((slot) => {
                    const card = slot.querySelector('.session');

                    if (! card) {
                        return;
                    }

                    maxHeight = Math.max(
                        maxHeight,
                        Math.ceil(card.getBoundingClientRect().height)
                    );
                });

                if (maxHeight <= 0) {
                    return;
                }

                laneSlots.forEach((slot) => {
                    slot.style.height = `${maxHeight}px`;
                });
            });
        });
    };

    window.addEventListener('load', alignCalendarLanes);

    let resizeTimer = null;

    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(alignCalendarLanes, 120);
    });
})();
</script>

@if ($viewMode === 'semaine')

    {{-- PORTAIL_VUE_SEMAINE_V1 --}}
    <div class="container week-view-container">

        <div class="week-toolbar">

            <div class="week-toolbar-actions">
                <a
                    class="button"
                    href="{{ $publicRoute(
                        'planificationstages.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semainePrecedente,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                    title="Semaine précédente"
                >
                    ←
                </a>
            </div>

            <div class="week-toolbar-center">
                <div class="week-title">
                    {{ $weekLabel }}
                </div>
            </div>

            <div class="week-toolbar-actions">
                <a
                    class="button"
                    href="{{ $publicRoute(
                        'planificationstages.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semaineActuelle,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                >
                    Aujourd’hui
                </a>

                <a
                    class="button"
                    href="{{ $publicRoute(
                        'planificationstages.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semaineSuivante,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                    title="Semaine suivante"
                >
                    →
                </a>
            </div>

        </div>

        <div class="week-calendar-scroll">
            <div class="week-calendar">

                <div class="week-days-header">
                    @foreach ($weekDays as $day)
                        <div class="week-day-header">
                            <span class="week-day-name">
                                {{ $day['jour'] }}
                            </span>
                            <span
                                class="week-day-number {{ $day['aujourdhui'] ? 'today' : '' }}"
                            >
                                {{ $day['numero'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @php
                    $weekHasSession = false;
                    $weekRows =
                        isset($weekDays[0]['cells'])
                            ? count($weekDays[0]['cells'])
                            : 0;
                @endphp

                <div class="week-events-grid">
                    @for ($lane = 0; $lane < $weekRows; $lane++)
                        @foreach ($weekDays as $day)
                            @php
                                $event = $day['cells'][$lane] ?? null;
                                if ($event) {
                                    $weekHasSession = true;
                                }
                            @endphp

                            <div class="week-cell">
                                @if ($event)
                                    @php
                                        $stageHue =
                                            (
                                                (int) sprintf(
                                                    '%u',
                                                    crc32(
                                                        (string) (
                                                            $event['stage']
                                                            ?? ''
                                                        )
                                                    )
                                                )
                                            )
                                            % 360;
                                    @endphp

                                    <div
                                        class="session {{ $event['complete'] ? 'full' : '' }}"
                                        title="{{ $event['stage_long'] ?? $event['stage'] }}"
                                        style="
                                            --stage-bg: hsl({{ $stageHue }} 80% 95%);
                                            --stage-border: hsl({{ $stageHue }} 70% 45%);
                                            --stage-text: hsl({{ $stageHue }} 72% 27%);
                                            --stage-button-hover: hsl({{ $stageHue }} 72% 37%);
                                        "
                                    >
                                        <div class="session-title">
                                            {{ $event['stage'] }}
                                        </div>

                                        @if (
                                            ($event['stage_long'] ?? null)
                                            && $event['stage_long'] !== $event['stage']
                                        )
                                            <div class="stage-long-tooltip">
                                                {{ $event['stage_long'] }}
                                            </div>
                                        @endif

                                        @if ($event['capacite'] !== null)
                                            @if ($event['complete'])
                                                <div class="places full">
                                                    Session complète
                                                </div>
                                            @else
                                                <div class="places available">
                                                    {{ $event['restantes'] }}
                                                    place(s) disponible(s)
                                                </div>
                                            @endif
                                        @endif

                                        <a
                                            href="{{ $event['inscription_url'] }}"
                                            class="register"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            @if ($event['complete'])
                                                Voir / liste d’attente
                                            @else
                                                Voir / s’inscrire
                                            @endif
                                        </a>
                                    </div>
                                @else
                                    <div class="week-empty-lane"></div>
                                @endif
                            </div>
                        @endforeach
                    @endfor
                </div>

                @if (! $weekHasSession)
                    <div class="week-no-session">
                        @if ($searchTerm !== '')
                            Aucun stage correspondant à
                            « {{ $searchTerm }} »
                            cette semaine.
                        @else
                            Aucun stage programmé cette semaine.
                        @endif
                    </div>
                @endif

            </div>
        </div>

    </div>

@endif
</body>
</html>coder@73cb0f96a544:~/app$
