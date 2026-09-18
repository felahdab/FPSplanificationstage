<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Besoin enregistré
    </title>

    <style>
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

        .card {
            max-width: 720px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
        }

        .success {
            font-size: 3rem;
        }

        .reference {
            margin: 1.25rem 0;
            padding: 1rem;
            border-radius: .7rem;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 1.2rem;
            font-weight: 750;
        }

        .info {
            color: #475569;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: .7rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .button {
            display: inline-flex;
            padding: .7rem 1rem;
            border-radius: .6rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-weight: 700;
        }

        .button-secondary {
            background: #475569;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="success">
        ✓
    </div>

    <h1>
        Votre expression de besoin a bien été enregistrée
    </h1>

    <div class="reference">
        Référence :
        {{ $besoin->code_besoin }}
    </div>

    <p class="info">
        Conservez cette référence.
        Elle vous permettra de consulter
        l’avancement de votre demande.
    </p>

    <p class="info">
        Demandeur :
        <strong>
            {{ $besoin->demandeur }}
        </strong>
    </p>

    <p class="info">
        Stage :
        <strong>
            {{ $besoin->stage?->libelle_court }}
        </strong>
    </p>

    <p class="info">
        Votre demande est maintenant transmise
        aux gestionnaires pour étude.
    </p>

    <div class="actions">

        <a
            class="button"
            href="{{ route(
                'planificationstages.public.besoin.suivi',
                [
                    'token' =>
                        $besoin->public_token
                ]
            ) }}"
        >
            Suivre ma demande
        </a>

        <a
            class="button button-secondary"
            href="{{ route(
                'planificationstages.public.calendrier'
            ) }}"
        >
            Retour au portail
        </a>

    </div>

</div>

</body>
</html>