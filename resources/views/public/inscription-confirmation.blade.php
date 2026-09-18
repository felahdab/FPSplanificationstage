<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inscription enregistrée
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
            max-width: 680px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .05);
        }

        .success {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        h1 {
            margin-top: 0;
        }

        .reference {
            padding: .8rem;
            margin: 1rem 0;
            background: #f8fafc;
            border-radius: .6rem;
            font-weight: 700;
        }

        .info {
            color: #475569;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="success">
        ✓
    </div>

    <h1>
        Votre demande a bien été enregistrée
    </h1>

    <div class="reference">
        Référence :
        {{ $inscription->code_inscription }}
    </div>

    <p class="info">
        Stage :
        <strong>
            {{ $inscription->sessionStage?->stage?->libelle_court }}
        </strong>
    </p>

    <p class="info">
        Session :
        {{ $inscription->sessionStage?->code_session }}
    </p>

    @if (
        $inscription->statut
        === 'liste_attente'
    )

        <p class="info">
            La session est actuellement complète.
            Votre demande a été placée en liste d’attente.
        </p>

    @elseif (
        $inscription->derogation_demandee
    )

        <p class="info">
            Votre demande nécessite l’étude d’une dérogation.
            Les gestionnaires du stage examineront votre demande.
        </p>

    @else

        <p class="info">
            Votre demande a été transmise aux gestionnaires du stage.
            Les modalités administratives vous seront communiquées ensuite.
        </p>

    @endif

    <p class="info">
        Vous pouvez conserver la référence ci-dessus.
    </p>

</div>

</body>
</html>