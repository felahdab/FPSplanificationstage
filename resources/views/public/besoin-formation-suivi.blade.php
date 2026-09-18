<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Suivi {{ $besoin->code_besoin }}
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
            max-width: 760px;
            margin: 0 auto;
        }

        .back {
            display: inline-flex;
            margin-bottom: 1rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 650;
        }

        .card {
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .05);
        }

        h1,
        h2 {
            margin-top: 0;
        }

        h2 {
            font-size: 1.15rem;
        }

        .reference {
            color: #64748b;
            font-weight: 700;
        }

        .status {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: .75rem;
            font-weight: 750;
        }

        .status-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .status-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .status-description {
            margin-top: .45rem;
            font-weight: 400;
            line-height: 1.5;
        }

        .row {
            padding: .7rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .row:last-child {
            border-bottom: none;
        }

        .label {
            display: block;
            margin-bottom: .15rem;
            color: #64748b;
            font-size: .85rem;
        }

        .value {
            font-weight: 650;
        }

        .session {
            padding: 1rem;
            border-radius: .75rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .button {
            display: inline-flex;
            margin-top: 1rem;
            padding: .7rem 1rem;
            border-radius: .6rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-weight: 750;
        }

        .button-green {
            background: #15803d;
        }
    </style>
</head>

<body>

<div class="container">

    <a
        class="back"
        href="{{ route(
            'planificationstages.public.calendrier'
        ) }}"
    >
        ← Retour au portail
    </a>

    <div class="card">

        <div class="reference">
            {{ $besoin->code_besoin }}
        </div>

        <h1>
            Suivi de votre expression de besoin
        </h1>

        <div
            class="
                status
                status-{{ $statutPublic['type'] }}
            "
        >

            {{ $statutPublic['label'] }}

            <div class="status-description">
                {{ $statutPublic['description'] }}
            </div>

        </div>

    </div>

    <div class="card">

        <h2>
            Votre demande
        </h2>

        <div class="row">

            <span class="label">
                Bâtiment / unité
            </span>

            <span class="value">
                {{ $besoin->demandeur }}
            </span>

        </div>

        <div class="row">

            <span class="label">
                Stage demandé
            </span>

            <span class="value">
                {{ $besoin->stage?->libelle_court }}
            </span>

        </div>

        <div class="row">

            <span class="label">
                Effectif
            </span>

            <span class="value">
                {{ $besoin->nombre_stagiaires }}
                stagiaire(s)
            </span>

        </div>

        <div class="row">

            <span class="label">
                Période demandée
            </span>

            <span class="value">
                Du
                {{ $besoin->date_debut_souhaitee?->format('d/m/Y') }}
                au
                {{ $besoin->date_fin_souhaitee?->format('d/m/Y') }}
            </span>

        </div>

    </div>

    @if (
        $besoin->statut === 'planifie'
        && $besoin->sessionStage
    )

        <div class="card">

            <h2>
                Session planifiée
            </h2>

            <div class="session">

                <div class="row">

                    <span class="label">
                        Session
                    </span>

                    <span class="value">
                        {{ $besoin->sessionStage->code_session }}
                    </span>

                </div>

                <div class="row">

                    <span class="label">
                        Date et heure
                    </span>

                    <span class="value">
                        Du
                        {{ $besoin->sessionStage->debut?->format('d/m/Y à H:i') }}
                        au
                        {{ $besoin->sessionStage->fin?->format('d/m/Y à H:i') }}
                    </span>

                </div>

                @if ($besoin->sessionStage->salle)

                    <div class="row">

                        <span class="label">
                            Salle
                        </span>

                        <span class="value">
                            {{ $besoin->sessionStage->salle->nom }}
                        </span>

                    </div>

                @endif

                <a
                    class="button button-green"
                    href="{{ route(
                        'planificationstages.public.inscription.create',
                        [
                            'session' =>
                                $besoin->sessionStage->id
                        ]
                    ) }}"
                >
                    Inscrire un stagiaire
                </a>

            </div>

        </div>

    @endif

</div>

</body>
</html>