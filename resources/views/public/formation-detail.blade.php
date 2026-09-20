<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $stage->libelle_court ?? 'Formation' }}</title>
    <style>
        body { margin:0; background:#f8fafc; color:#0f172a; font-family:Inter,system-ui,sans-serif; }
        .page { width:min(1000px,calc(100% - 32px)); margin:28px auto 60px; }
        .back { display:inline-block; margin-bottom:18px; color:#1d4ed8; font-weight:700; text-decoration:none; }
        .card { margin-bottom:16px; padding:24px; border:1px solid #e2e8f0; border-radius:16px; background:#fff; }
        h1,h2 { margin-top:0; }
        .code { margin-bottom:6px; color:#2563eb; font-weight:800; }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
        .full { grid-column:1 / -1; }
        .label { display:block; margin-bottom:4px; color:#64748b; font-size:12px; font-weight:700; }
        .value { white-space:pre-line; line-height:1.55; }
        .button { display:inline-flex; min-height:46px; padding:0 20px; align-items:center; justify-content:center; border-radius:10px; background:#2563eb; color:#fff; font-weight:800; text-decoration:none; }
        .closed { color:#b91c1c; font-weight:800; }
        @media (max-width:700px) { .grid { grid-template-columns:1fr; } .full { grid-column:auto; } .button { width:100%; } }
    </style>
</head>
<body>
<main class="page">
    <a class="back" href="{{ $retourUrl }}">← Retour au planning des formations</a>

    <section class="card">
        <div class="code">{{ $stage->code_stage }}</div>
        <h1>{{ $stage->libelle_court ?? 'Formation' }}</h1>
        @if (filled($stage->libelle_long))
            <div class="value">{{ $stage->libelle_long }}</div>
        @endif
    </section>

    <section class="card">
        <h2>Session</h2>
        <div class="grid">
            <div>
                <span class="label">Début</span>
                <div class="value">{{ $session->debut->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <span class="label">Fin</span>
                <div class="value">{{ $session->fin->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <span class="label">Lieu</span>
                <div class="value">{{ $stage->lieux_formation ?: $stage->centre_formation ?: $session->salle?->nom ?: 'Non renseigné' }}</div>
            </div>
            <div>
                <span class="label">Places restantes</span>
                <div class="value">{{ $session->places_restantes ?? 'Non renseigné' }}</div>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Description de la formation</h2>
        <div class="grid">
            <div class="full">
                <span class="label">Objectif</span>
                <div class="value">{{ $stage->objectif_formation ?: 'Non renseigné' }}</div>
            </div>
            <div class="full">
                <span class="label">Fonctions visées</span>
                <div class="value">{{ $stage->fonctions_visees ?: 'Non renseignées' }}</div>
            </div>
            <div>
                <span class="label">Service émetteur</span>
                <div class="value">{{ $stage->service_emetteur ?: 'Non renseigné' }}</div>
            </div>
            <div>
                <span class="label">Durée</span>
                <div class="value">
                    @if ($stage->duree_jours !== null)
                        {{ rtrim(rtrim(number_format((float) $stage->duree_jours, 1, ',', ''), '0'), ',') }} jour{{ (float) $stage->duree_jours > 1 ? 's' : '' }}
                    @else
                        Non renseignée
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Pré-requis</h2>
        @if ($stage->prerequis->isNotEmpty())
            <ul>
                @foreach ($stage->prerequis as $prerequis)
                    <li>
                        {{ $prerequis->libelle }}
                        @if ($prerequis->obligatoire)
                            <strong>— obligatoire</strong>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>Aucun pré-requis renseigné.</p>
        @endif

        @if ($inscriptionPossible)
            <p style="margin-top:26px;">
                <a class="button" href="{{ $inscriptionUrl }}">Je m'inscris à cette formation</a>
            </p>
        @else
            <p class="closed">Les inscriptions ne sont pas ouvertes pour cette session.</p>
        @endif
    </section>
</main>
</body>
</html>
