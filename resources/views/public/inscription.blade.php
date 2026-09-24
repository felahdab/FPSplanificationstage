<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inscription au stage
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
            max-width: 850px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .05);
        }

        h1 {
            margin-top: 0;
            margin-bottom: .5rem;
            font-size: 1.8rem;
        }

        h2 {
            margin-top: 0;
            font-size: 1.15rem;
        }

        .stage-name {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .stage-info {
            margin-top: .4rem;
            color: #475569;
        }

        .grid {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .field {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: .35rem;
            font-weight: 650;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: .7rem .8rem;
            border: 1px solid #cbd5e1;
            border-radius: .55rem;
            font: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .prerequis {
            padding: .8rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .prerequis:last-child {
            border-bottom: none;
        }

        .checkbox-line {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
        }

        .checkbox-line input {
            margin-top: .25rem;
            width: 1.1rem;
            height: 1.1rem;
        }

        .required {
            color: #b91c1c;
            font-size: .85rem;
            font-weight: 700;
        }

        .optional {
            color: #64748b;
            font-size: .85rem;
        }

        .derogation {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: .7rem;
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }

        .errors {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: .7rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .errors ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .button {
            appearance: none;
            border: none;
            border-radius: .65rem;
            padding: .8rem 1.2rem;
            background: #2563eb;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .help {
            color: #64748b;
            font-size: .9rem;
        }

        @media (max-width: 700px) {
            .grid {
                grid-template-columns: 1fr;
            }

            body {
                padding: 1rem .6rem;
            }
        }
    
        /* RETOUR_CALENDRIER_INSCRIPTION_V1 */
        .back-calendar {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: 1rem;
            padding: .65rem .9rem;
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: white;
            color: #334155;
            text-decoration: none;
            font-size: .92rem;
            font-weight: 700;
        }

        .back-calendar:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
</style>
</head>

<body>

<div class="container">

    {{-- RETOUR_CALENDRIER_INSCRIPTION_V1 --}}
    <a
        class="back-calendar"
        href="{{ route('fpsplanificationstage.public.calendrier') }}"
    >
        ← Retour au calendrier
    </a>

    <div class="card">

        <h1>
            Inscription au stage
        </h1>

        <div class="stage-name">
            {{ $session->stage?->libelle_court }}
        </div>

        <div class="stage-info">
            Session :
            {{ $session->code_session }}
        </div>

        <div class="stage-info">
            Du
            {{ $session->debut?->format('d/m/Y à H:i') }}
            au
            {{ $session->fin?->format('d/m/Y à H:i') }}
        </div>

        @if ($session->salle)
            <div class="stage-info">
                Salle :
                {{ $session->salle->nom }}
            </div>
        @endif

    </div>

    @if ($errors->any())

        <div class="errors">
            <strong>
                L’inscription n’a pas pu être enregistrée.
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>

    @endif

    <form
        method="POST"
        action="{{ route(
            'fpsplanificationstage.public.inscription.store',
            ['session' => $session->id]
        ) }}"
    >

        @csrf

        <div class="card">

            <h2>
                Vos informations
            </h2>

            <div class="grid">

                <div class="field">
                    <label for="nom">
                        Nom *
                    </label>

                    <input
                        id="nom"
                        type="text"
                        name="nom"
                        value="{{ old('nom', $identity['nom'] ?? null) }}"
                        readonly
                        required
                    >
                </div>

                <div class="field">
                    <label for="prenom">
                        Prénom *
                    </label>

                    <input
                        id="prenom"
                        type="text"
                        name="prenom"
                        value="{{ old('prenom', $identity['prenom'] ?? null) }}"
                        readonly
                        required
                    >
                </div>

                                {{-- GRADE_MENU_DEROULANT_V1_1_PUBLIC --}}
                <div class="field">
                    <label for="grade">
                        Grade
                    </label>

                    <select
                        id="grade"
                        name="grade"
                    >
                        <option value="">
                            Sélectionner un grade
                        </option>

                        @foreach ($grades as $grade)
                            <option
                                value="{{ $grade->libelle_court }}"
                                @selected(old('grade', $identity['grade'] ?? null) === $grade->libelle_court)
                            >
                                {{ $grade->libelle_long ?: $grade->libelle_court }}
                                @if ($grade->libelle_long && $grade->libelle_long !== $grade->libelle_court)
                                    — {{ $grade->libelle_court }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- INSCRIPTION_IDENTITE_V1_PUBLIC --}}

                <div class="field">
                    <label for="matricule">
                        Matricule
                    </label>

                    <input
                        id="matricule"
                        type="text"
                        name="matricule"
                        value="{{ old('matricule', $identity['matricule'] ?? null) }}"
                        maxlength="100"
                    >
                </div>

                <div class="field">
                    <label for="nid">
                        NID
                    </label>

                    <input
                        id="nid"
                        type="text"
                        name="nid"
                        value="{{ old('nid', $identity['nid'] ?? null) }}"
                        maxlength="100"
                    >
                </div>

                <div class="field">
                    <label for="brevet">
                        Brevet
                    </label>

                    <select
                        id="brevet"
                        name="brevet"
                    >
                        <option value="">
                            Sélectionner un brevet
                        </option>

                        @foreach ($brevets as $brevet)
                            <option
                                value="{{ $brevet->libelle_court }}"
                                @selected(old('brevet', $identity['brevet'] ?? null) === $brevet->libelle_court)
                            >
                                {{ $brevet->libelle_long ?: $brevet->libelle_court }}
                                @if ($brevet->libelle_long && $brevet->libelle_long !== $brevet->libelle_court)
                                    — {{ $brevet->libelle_court }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="specialite">
                        Spécialité
                    </label>

                    <select
                        id="specialite"
                        name="specialite"
                    >
                        <option value="">
                            Sélectionner une spécialité
                        </option>

                        @foreach ($specialites as $specialite)
                            <option
                                value="{{ $specialite->libelle_court }}"
                                @selected(old('specialite', $identity['specialite'] ?? null) === $specialite->libelle_court)
                            >
                                {{ $specialite->libelle_long ?: $specialite->libelle_court }}
                                @if ($specialite->libelle_long && $specialite->libelle_long !== $specialite->libelle_court)
                                    — {{ $specialite->libelle_court }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="unite">
                        Bâtiment / unité *
                    </label>

                    <input
                        id="unite"
                        type="text"
                        name="unite"
                        value="{{ old('unite', $identity['unite'] ?? null) }}"
                        required
                    >
                </div>

                <div class="field">
                    <label for="email">
                        E-mail *
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $identity['email'] ?? null) }}"
                        readonly
                        required
                    >
                </div>

                <div class="field">
                    <label for="telephone">
                        Téléphone
                    </label>

                    <input
                        id="telephone"
                        type="tel"
                        name="telephone"
                        value="{{ old('telephone', $identity['telephone'] ?? null) }}"
                    >
                </div>


            </div>

        </div>

        <div class="card">

            <h2>
                Prérequis
            </h2>

            @if (
                $session->stage
                && $session->stage->prerequis->isNotEmpty()
            )

                <p class="help">
                    Cochez uniquement les prérequis que vous remplissez réellement.
                </p>

                @foreach ($session->stage->prerequis as $prerequis)

                    <div class="prerequis">

                        <label class="checkbox-line">

                            <input
                                type="checkbox"
                                name="prerequis[{{ $prerequis->id }}]"
                                value="1"
                                @checked(
                                    old(
                                        'prerequis.'
                                        . $prerequis->id
                                    )
                                )
                            >

                            <span>
                                {{ $prerequis->libelle }}

                                @if ($prerequis->obligatoire)
                                    <span class="required">
                                        — obligatoire
                                    </span>
                                @else
                                    <span class="optional">
                                        — facultatif
                                    </span>
                                @endif
                            </span>

                        </label>

                    </div>

                @endforeach

            @else

                <p>
                    Aucun prérequis n’est renseigné pour ce stage.
                </p>

            @endif

            <div class="derogation">

                <label class="checkbox-line">

                    <input
                        type="checkbox"
                        id="demande_derogation"
                        name="demande_derogation"
                        value="1"
                        @checked(
                            old('demande_derogation')
                        )
                    >

                    <span>
                        Je ne remplis pas tous les prérequis obligatoires et je souhaite demander une dérogation.
                    </span>

                </label>

                <div
                    class="field"
                    style="margin-top: 1rem;"
                >
                    <label for="derogation_motif">
                        Justification de la demande
                    </label>

                    <textarea
                        id="derogation_motif"
                        name="derogation_motif"
                    >{{ old('derogation_motif') }}</textarea>
                </div>

            </div>

        </div>

        <div class="card">

            <p class="help">
                Après validation, votre demande sera transmise aux gestionnaires du stage.
            </p>

            <button
                type="submit"
                class="button"
            >
                Envoyer ma demande d’inscription
            </button>

        </div>

    </form>

</div>

</body>
</html>
