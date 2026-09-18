<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Suivre une expression de besoin
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
            max-width: 650px;
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
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .05);
        }

        h1 {
            margin-top: 0;
        }

        .intro {
            color: #475569;
            line-height: 1.55;
        }

        .field {
            margin-top: 1rem;
        }

        label {
            display: block;
            margin-bottom: .35rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: .75rem .8rem;
            border: 1px solid #cbd5e1;
            border-radius: .55rem;
            font: inherit;
        }

        .button {
            margin-top: 1.5rem;
            padding: .8rem 1.2rem;
            border: 0;
            border-radius: .65rem;
            background: #2563eb;
            color: white;
            font: inherit;
            font-weight: 750;
            cursor: pointer;
        }

        .errors {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #fecaca;
            border-radius: .7rem;
            background: #fef2f2;
            color: #991b1b;
        }

        .help {
            margin-top: .35rem;
            color: #64748b;
            font-size: .88rem;
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

    @if ($errors->any())

        <div class="errors">

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif

    <div class="card">

        <h1>
            Suivre mon expression de besoin
        </h1>

        <p class="intro">
            Saisissez la référence obtenue lors de
            votre demande ainsi que l’adresse e-mail
            du contact déclarée.
        </p>

        <form
            method="POST"
            action="{{ route(
                'planificationstages.public.besoin.suivi.rechercher'
            ) }}"
        >

            @csrf

            <div class="field">

                <label for="code_besoin">
                    Référence du besoin
                </label>

                <input
                    id="code_besoin"
                    type="text"
                    name="code_besoin"
                    value="{{ old('code_besoin') }}"
                    placeholder="BES-000014"
                    required
                    autocomplete="off"
                >

                <div class="help">
                    Exemple : BES-000014
                </div>

            </div>

            <div class="field">

                <label for="contact_email">
                    Adresse e-mail
                </label>

                <input
                    id="contact_email"
                    type="email"
                    name="contact_email"
                    value="{{ old('contact_email') }}"
                    required
                >

            </div>

            <button
                type="submit"
                class="button"
            >
                Consulter l’avancement
            </button>

        </form>

    </div>

</div>

</body>
</html>