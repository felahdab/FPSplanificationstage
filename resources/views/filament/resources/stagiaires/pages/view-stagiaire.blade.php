<x-filament-panels::page>

    @php
        $stagiaire = $this->getRecord();

        $inscriptions = \Modules\FPSplanificationstage\Models\Inscription::query()
            ->where('stagiaire_id', $stagiaire->getKey())
            ->with([
                'sessionStage.stage',
                'sessionStage.salle',
            ])
            ->get()
            ->sortByDesc(
                fn ($inscription) =>
                    $inscription
                        ->sessionStage
                        ?->debut
                        ?->timestamp
                    ?? $inscription
                        ->created_at
                        ?->timestamp
                    ?? 0
            );

        $participations = $inscriptions
            ->where(
                'presence',
                'present'
            )
            ->count();

        $presenceLabels = [
            'present' =>
                'Présent',
            'absent' =>
                'Absent',
            'non_renseignee' =>
                'Non renseignée',
        ];
    @endphp

    <div class="space-y-6">

        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(180px,1fr));
                gap:1rem;
            "
        >
            <div
                style="
                    padding:1rem;
                    border:1px solid #e5e7eb;
                    border-radius:.75rem;
                "
            >
                <div
                    style="
                        font-size:.8rem;
                        color:#64748b;
                    "
                >
                    Nom / prénom
                </div>

                <strong>
                    {{ trim($stagiaire->nom . ' ' . $stagiaire->prenom) }}
                </strong>
            </div>

            <div
                style="
                    padding:1rem;
                    border:1px solid #e5e7eb;
                    border-radius:.75rem;
                "
            >
                <div
                    style="
                        font-size:.8rem;
                        color:#64748b;
                    "
                >
                    NID
                </div>

                <strong>
                    {{ $stagiaire->nid ?: '—' }}
                </strong>
            </div>

            <div
                style="
                    padding:1rem;
                    border:1px solid #e5e7eb;
                    border-radius:.75rem;
                "
            >
                <div
                    style="
                        font-size:.8rem;
                        color:#64748b;
                    "
                >
                    Matricule
                </div>

                <strong>
                    {{ $stagiaire->matricule ?: '—' }}
                </strong>
            </div>

            <div
                style="
                    padding:1rem;
                    border:1px solid #e5e7eb;
                    border-radius:.75rem;
                "
            >
                <div
                    style="
                        font-size:.8rem;
                        color:#64748b;
                    "
                >
                    Stages suivis
                </div>

                <strong>
                    {{ $participations }}
                </strong>
            </div>
        </div>

        <div
            style="
                padding:1rem;
                border:1px solid #e5e7eb;
                border-radius:.75rem;
            "
        >
            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(auto-fit,minmax(180px,1fr));
                    gap:.75rem 1.5rem;
                "
            >
                <div>
                    <strong>Grade :</strong>
                    {{ $stagiaire->grade?->libelle_court ?: '—' }}
                </div>

                <div>
                    <strong>Brevet :</strong>
                    {{ $stagiaire->brevet?->libelle_court ?: '—' }}
                </div>

                <div>
                    <strong>Spécialité :</strong>
                    {{ $stagiaire->specialite?->libelle_court ?: '—' }}
                </div>

                <div>
                    <strong>Bâtiment / unité :</strong>
                    {{ $stagiaire->unite?->libelle_court ?: '—' }}
                </div>

                <div>
                    <strong>E-mail :</strong>
                    {{ $stagiaire->email ?: '—' }}
                </div>

                <div>
                    <strong>Téléphone :</strong>
                    {{ $stagiaire->telephone ?: '—' }}
                </div>
            </div>
        </div>

        <div
            style="
                overflow-x:auto;
                border:1px solid #e5e7eb;
                border-radius:.75rem;
            "
        >
            <div
                style="
                    padding:1rem;
                    font-size:1.05rem;
                    font-weight:700;
                    border-bottom:1px solid #e5e7eb;
                "
            >
                Historique des inscriptions et participations
            </div>

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    min-width:900px;
                "
            >
                <thead>
                    <tr
                        style="
                            text-align:left;
                            background:#f8fafc;
                        "
                    >
                        <th style="padding:.75rem;">
                            Date
                        </th>

                        <th style="padding:.75rem;">
                            Stage
                        </th>

                        <th style="padding:.75rem;">
                            Session
                        </th>

                        <th style="padding:.75rem;">
                            Salle
                        </th>

                        <th style="padding:.75rem;">
                            Candidature
                        </th>

                        <th style="padding:.75rem;">
                            Présence
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($inscriptions as $inscription)

                        <tr
                            style="
                                border-top:1px solid #e5e7eb;
                            "
                        >
                            <td style="padding:.75rem;">
                                {{
                                    $inscription
                                        ->sessionStage
                                        ?->debut
                                        ?->format('d/m/Y')
                                    ?? '—'
                                }}
                            </td>

                            <td style="padding:.75rem;">
                                <strong>
                                    {{
                                        $inscription
                                            ->sessionStage
                                            ?->stage
                                            ?->libelle_court
                                        ?? '—'
                                    }}
                                </strong>
                            </td>

                            <td style="padding:.75rem;">
                                {{
                                    $inscription
                                        ->sessionStage
                                        ?->code_session
                                    ?? '—'
                                }}
                            </td>

                            <td style="padding:.75rem;">
                                {{
                                    $inscription
                                        ->sessionStage
                                        ?->salle
                                        ?->nom
                                    ?? '—'
                                }}
                            </td>

                            <td style="padding:.75rem;">
                                {{
                                    match (
                                        $inscription->statut
                                    ) {
                                        'attente_nemo' =>
                                            'Attente NEMO',
                                        'confirmee' =>
                                            'Confirmée',
                                        'attente_derogation' =>
                                            'Attente dérogation',
                                        'liste_attente' =>
                                            'Liste d’attente',
                                        'refusee' =>
                                            'Refusée',
                                        'annulee' =>
                                            'Annulée',
                                        default =>
                                            $inscription->statut
                                            ?: '—',
                                    }
                                }}
                            </td>

                            <td style="padding:.75rem;">
                                {{
                                    $presenceLabels[
                                        $inscription
                                            ->presence
                                        ?? 'non_renseignee'
                                    ]
                                    ?? 'Non renseignée'
                                }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                style="
                                    padding:1rem;
                                    color:#64748b;
                                "
                            >
                                Aucun historique disponible.
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            style="
                padding:.9rem 1rem;
                border-radius:.75rem;
                background:#f8fafc;
                color:#475569;
                font-size:.9rem;
            "
        >
            « Présent » signifie que la participation au stage
            a été confirmée administrativement.
            Une candidature « Confirmée » seule ne suffit pas
            à compter le stage comme réellement suivi.
        </div>

    </div>

</x-filament-panels::page>
