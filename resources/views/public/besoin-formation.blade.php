<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Expression de besoin en stage
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
            max-width: 900px;
            margin: 0 auto;
        }

        .topbar {
            margin-bottom: 1rem;
        }

        .back {
            display: inline-flex;
            color: #2563eb;
            text-decoration: none;
            font-weight: 650;
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
        }

        h2 {
            margin-top: 0;
            font-size: 1.15rem;
        }

        .intro {
            color: #475569;
            line-height: 1.55;
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

        input,
        select,
        textarea {
            width: 100%;
            padding: .72rem .8rem;
            border: 1px solid #cbd5e1;
            border-radius: .55rem;
            background: white;
            color: #0f172a;
            font: inherit;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .help {
            margin-top: .35rem;
            color: #64748b;
            font-size: .88rem;
            line-height: 1.4;
        }

        .period-info {
            padding: 1rem;
            border: 1px solid #dbeafe;
            border-radius: .7rem;
            background: #eff6ff;
            color: #1e3a8a;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .errors {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #fecaca;
            border-radius: .7rem;
            background: #fef2f2;
            color: #991b1b;
        }

        .errors ul {
            margin-bottom: 0;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .8rem 1.2rem;
            border: none;
            border-radius: .65rem;
            background: #2563eb;
            color: white;
            font: inherit;
            font-weight: 750;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .required {
            color: #b91c1c;
        }

        @media (max-width: 700px) {
            body {
                padding: 1rem .6rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topbar">

        <a
            class="back"
            href="{{ route(
                'planificationstages.public.calendrier'
            ) }}"
        >
            ← Retour au portail des formations
        </a>

    </div>

    <div class="card">

        <h1>
            Expression de besoin en stage
        </h1>

        <p class="intro">
            Ce formulaire permet à un bâtiment ou une unité
            de transmettre directement un besoin de formation
            aux gestionnaires.
        </p>

        <p class="intro">
            Pour une campagne comprenant de nombreux besoins,
            l’import Excel pourra toujours être utilisé.
        </p>

    </div>

    @if ($errors->any())

        <div class="errors">

            <strong>
                Le besoin n’a pas pu être enregistré.
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
            'planificationstages.public.besoin.store'
        ) }}"
    >

        @csrf

        <div class="card">

            <h2>
                Demandeur
            </h2>

            <div class="field">

                <label for="demandeur">
                    Bâtiment / unité
                    <span class="required">*</span>
                </label>

                <input
                    id="demandeur"
                    type="text"
                    name="demandeur"
                    value="{{ old('demandeur') }}"
                    placeholder="Ex. FDA FORBIN"
                    required
                >

            </div>

            <div class="grid">

                <div class="field">

                    <label for="contact_nom">
                        Nom du contact
                        <span class="required">*</span>
                    </label>

                    <input
                        id="contact_nom"
                        type="text"
                        name="contact_nom"
                        value="{{ old('contact_nom') }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="contact_email">
                        E-mail
                        <span class="required">*</span>
                    </label>

                    <input
                        id="contact_email"
                        type="email"
                        name="contact_email"
                        value="{{ old('contact_email') }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="contact_telephone">
                        Téléphone
                    </label>

                    <input
                        id="contact_telephone"
                        type="tel"
                        name="contact_telephone"
                        value="{{ old('contact_telephone') }}"
                    >

                </div>

            </div>

        </div>

        <div class="card">

            <h2>
                Stage demandé
            </h2>

            <div class="field">

                <label for="stage_id">
                    Stage
                    <span class="required">*</span>
                </label>

                <select
                    id="stage_id"
                    name="stage_id"
                    required
                >

                    <option value="">
                        Sélectionnez un stage
                    </option>

                    @foreach ($stages as $stage)

                        <option
                            value="{{ $stage->id }}"
                            @selected(
                                (string) old('stage_id')
                                === (string) $stage->id
                            )
                        >
                            @if ($stage->code_stage)
                                {{ $stage->code_stage }} —
                            @endif

                            {{ $stage->libelle_court }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="field">

                <label for="nombre_stagiaires">
                    Nombre de stagiaires
                    <span class="required">*</span>
                </label>

                <input
                    id="nombre_stagiaires"
                    type="number"
                    name="nombre_stagiaires"
                    min="1"
                    max="999"
                    value="{{ old(
                        'nombre_stagiaires',
                        1
                    ) }}"
                    required
                >

            </div>

        </div>

        <div class="card">

            <h2>
                Période souhaitée
            </h2>

            <div class="period-info">
                <strong>
                    Plage de dates :
                </strong>
                vous indiquez une période durant laquelle
                le stage peut être programmé.
                C’est le choix conseillé lorsque vous êtes flexible.
                <br><br>

                <strong>
                    Dates imposées :
                </strong>
                le stage doit avoir lieu exactement
                sur les dates indiquées.
            </div>

            <div class="field">

                <label for="type_periode">
                    Type de demande
                    <span class="required">*</span>
                </label>

                <select
                    id="type_periode"
                    name="type_periode"
                    required
                >

                    <option
                        value="plage"
                        @selected(
                            old(
                                'type_periode',
                                'plage'
                            )
                            === 'plage'
                        )
                    >
                        Plage de dates
                    </option>

                    <option
                        value="dates_fixes"
                        @selected(
                            old('type_periode')
                            === 'dates_fixes'
                        )
                    >
                        Dates imposées
                    </option>

                </select>

            </div>

            <div class="grid">

                <div class="field">

                    <label for="date_debut_souhaitee">
                        Date de début
                        <span class="required">*</span>
                    </label>

                    <input
                        id="date_debut_souhaitee"
                        type="date"
                        name="date_debut_souhaitee"
                        value="{{ old(
                            'date_debut_souhaitee'
                        ) }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="date_fin_souhaitee">
                        Date de fin
                        <span class="required">*</span>
                    </label>

                    <input
                        id="date_fin_souhaitee"
                        type="date"
                        name="date_fin_souhaitee"
                        value="{{ old(
                            'date_fin_souhaitee'
                        ) }}"
                        required
                    >

                </div>

            </div>

        </div>

        <div class="card">

            <h2>
                Priorité
            </h2>

            <div class="field">

                <label for="priorite">
                    Niveau de priorité
                </label>

                <select
                    id="priorite"
                    name="priorite"
                    required
                >

                    <option
                        value="normale"
                        @selected(
                            old(
                                'priorite',
                                'normale'
                            )
                            === 'normale'
                        )
                    >
                        Normale
                    </option>

                    <option
                        value="haute"
                        @selected(
                            old('priorite')
                            === 'haute'
                        )
                    >
                        Haute
                    </option>

                    <option
                        value="urgente"
                        @selected(
                            old('priorite')
                            === 'urgente'
                        )
                    >
                        Urgente
                    </option>

                </select>

                <div class="help">
                    La priorité sera examinée par les gestionnaires
                    lors de la planification.
                </div>

            </div>

        </div>

        <div class="card">

            <h2>
                Contraintes ou informations complémentaires
            </h2>

            <div class="field">

                <label for="commentaire">
                    Commentaire
                </label>

                <textarea
                    id="commentaire"
                    name="commentaire"
                    placeholder="Indiquez ici les contraintes particulières, périodes à éviter ou toute autre information utile."
                >{{ old('commentaire') }}</textarea>

            </div>

        </div>

        <div class="card">

            <div class="help">
                Après validation, votre besoin sera transmis
                aux gestionnaires pour étude et planification.
            </div>

            <br>

            <button
                type="submit"
                class="button"
            >
                Envoyer mon expression de besoin
            </button>

        </div>

    </form>

</div>


{{-- BESOIN_PLAGE_DUREE_STAGE_PUBLIC_V1_2 --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stageSelect = document.querySelector('[name="stage_id"]');
    const typeSelect = document.querySelector('[name="type_periode"]');
    const startInput = document.querySelector('[name="date_debut_souhaitee"]');
    const endInput = document.querySelector('[name="date_fin_souhaitee"]');

    if (!stageSelect || !typeSelect || !startInput || !endInput) {
        return;
    }

    const durations = @json(
        collect($stages ?? [])->pluck('duree_jours', 'id')
    );

    const labels = {
        dates_fixes: 'Date de début imposée',
        plage: 'Plage de disponibilité',
        plage_demarrage: 'Plage de démarrage',
    };

    Object.entries(labels).forEach(([value, label]) => {
        let option = Array.from(typeSelect.options)
            .find(item => item.value === value);

        if (!option) {
            option = document.createElement('option');
            option.value = value;
            typeSelect.appendChild(option);
        }

        option.textContent = label;
    });

    let help = document.getElementById('besoin-periode-help-v1-1');

    if (!help) {
        help = document.createElement('div');
        help.id = 'besoin-periode-help-v1-1';
        help.style.marginTop = '.45rem';
        help.style.fontSize = '.9rem';
        help.style.lineHeight = '1.35';
        endInput.insertAdjacentElement('afterend', help);
    }

    function durationDays() {
        const value = Number(durations[stageSelect.value]);
        return Number.isFinite(value) ? value : 0;
    }

    function requiredWorkingDays() {
        const duration = durationDays();
        return duration > 0 ? Math.max(1, Math.ceil(duration)) : 0;
    }

    function parseDate(value) {
        if (!value) return null;
        const parts = value.split('-');
        if (parts.length !== 3) return null;
        return new Date(
            Number(parts[0]),
            Number(parts[1]) - 1,
            Number(parts[2]),
            12, 0, 0
        );
    }

    function isoDate(date) {
        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0'),
        ].join('-');
    }

    function formatDate(date) {
        return [
            String(date.getDate()).padStart(2, '0'),
            String(date.getMonth() + 1).padStart(2, '0'),
            date.getFullYear(),
        ].join('/');
    }

    function isWeekend(date) {
        return date.getDay() === 0 || date.getDay() === 6;
    }

    function countWorkingDays(start, end) {
        if (!start || !end || end < start) return 0;
        const cursor = new Date(start);
        let count = 0;

        while (cursor <= end) {
            if (!isWeekend(cursor)) count++;
            cursor.setDate(cursor.getDate() + 1);
        }

        return count;
    }

    function workingEndDate(start, required) {
        if (!start || required <= 0) return null;
        const cursor = new Date(start);

        while (isWeekend(cursor)) {
            cursor.setDate(cursor.getDate() + 1);
        }

        let remaining = required - 1;

        while (remaining > 0) {
            cursor.setDate(cursor.getDate() + 1);
            if (!isWeekend(cursor)) remaining--;
        }

        return cursor;
    }

    function refresh() {
        const type = typeSelect.value;
        const duration = durationDays();
        const required = requiredWorkingDays();
        const start = parseDate(startInput.value);
        let end = parseDate(endInput.value);

        endInput.removeAttribute('min');
        endInput.readOnly = false;
        endInput.required = type !== 'dates_fixes';

        if (duration <= 0) {
            help.textContent = 'La durée du stage doit être renseignée dans le catalogue.';
            help.style.color = '#b91c1c';
            return false;
        }

        if (type === 'dates_fixes') {
            if (!start) {
                help.textContent = 'Choisissez la date de début imposée ; la fin sera calculée automatiquement.';
                help.style.color = '#64748b';
                return false;
            }

            if (isWeekend(start)) {
                help.textContent = 'La date de début imposée doit être un jour ouvré.';
                help.style.color = '#b91c1c';
                return false;
            }

            const calculatedEnd = workingEndDate(start, required);

            if (!calculatedEnd) return false;

            endInput.value = isoDate(calculatedEnd);
            endInput.readOnly = true;
            help.textContent =
                `Fin calculée automatiquement : ${formatDate(calculatedEnd)} `
                + `pour un stage de ${duration} jour(s).`;
            help.style.color = '#166534';
            return true;
        }

        if (!start || !end) {
            help.textContent = type === 'plage'
                ? `La plage de disponibilité doit couvrir au moins ${required} jour(s) ouvré(s).`
                : 'Indiquez le premier et le dernier jour possibles pour démarrer le stage.';
            help.style.color = '#64748b';
            return false;
        }

        if (end < start) {
            help.textContent = 'La fin de la plage doit être postérieure ou égale à son début.';
            help.style.color = '#b91c1c';
            return false;
        }

        if (type === 'plage') {
            const minEnd = workingEndDate(start, required);
            if (minEnd) endInput.min = isoDate(minEnd);

            const selected = countWorkingDays(start, end);

            if (selected < required) {
                help.textContent =
                    `Plage de disponibilité trop courte : ${selected} jour(s) ouvré(s), `
                    + `minimum ${required}`
                    + (minEnd ? ` — fin minimale : ${formatDate(minEnd)}.` : '.');
                help.style.color = '#b91c1c';
                return false;
            }

            help.textContent =
                `Plage valide : ${selected} jour(s) ouvré(s). `
                + 'Le stage complet devra tenir dans cette fenêtre.';
            help.style.color = '#166534';
            return true;
        }

        if (type === 'plage_demarrage') {
            endInput.min = isoDate(start);
            help.textContent =
                'Plage de démarrage valide : le stage pourra commencer n’importe quel jour ouvré de cette fenêtre et se terminer après celle-ci.';
            help.style.color = '#166534';
            return true;
        }

        help.textContent = 'Type de période invalide.';
        help.style.color = '#b91c1c';
        return false;
    }

    [stageSelect, typeSelect, startInput, endInput].forEach(element => {
        element.addEventListener('change', refresh);
        element.addEventListener('input', refresh);
    });

    const form = typeSelect.closest('form');

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!refresh()) {
                event.preventDefault();
            }
        });
    }

    refresh();
});
</script>
</body>
</html>