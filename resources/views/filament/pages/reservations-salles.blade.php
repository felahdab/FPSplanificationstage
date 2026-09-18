<x-filament-panels::page>
    <style>
        .rs-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    5,
                    minmax(
                        0,
                        1fr
                    )
                );
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .rs-card,
        .rs-panel {
            border:
                1px solid
                rgb(226 232 240);
            border-radius: .8rem;
            background: white;
        }

        .dark .rs-card,
        .dark .rs-panel {
            border-color:
                rgb(51 65 85);
            background:
                rgb(15 23 42);
        }

        .rs-card {
            padding: 1rem;
        }

        .rs-card-label {
            color:
                rgb(100 116 139);
            font-size: .78rem;
            font-weight: 700;
            text-transform:
                uppercase;
            letter-spacing: .04em;
        }

        .rs-card-value {
            margin-top: .35rem;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .rs-card-small {
            margin-top: .25rem;
            color:
                rgb(100 116 139);
            font-size: .82rem;
            line-height: 1.35;
        }

        .rs-panel {
            margin-bottom: 1.25rem;
            overflow: hidden;
        }

        .rs-panel-head {
            display: flex;
            align-items: center;
            justify-content:
                space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border-bottom:
                1px solid
                rgb(226 232 240);
        }

        .dark .rs-panel-head {
            border-bottom-color:
                rgb(51 65 85);
        }

        .rs-panel-title {
            font-size: 1rem;
            font-weight: 800;
        }

        .rs-panel-subtitle {
            margin-top: .2rem;
            color:
                rgb(100 116 139);
            font-size: .82rem;
        }

        .rs-filters {
            display: flex;
            gap: .6rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .rs-field label {
            display: block;
            margin-bottom: .25rem;
            color:
                rgb(71 85 105);
            font-size: .78rem;
            font-weight: 700;
        }

        .dark .rs-field label {
            color:
                rgb(203 213 225);
        }

        .rs-field select,
        .rs-field input {
            min-height: 2.45rem;
            padding: .45rem .65rem;
            border:
                1px solid
                rgb(203 213 225);
            border-radius: .5rem;
            background: white;
            color:
                rgb(15 23 42);
        }

        .dark .rs-field select,
        .dark .rs-field input {
            border-color:
                rgb(71 85 105);
            background:
                rgb(30 41 59);
            color: white;
        }

        .rs-reset {
            min-height: 2.45rem;
            padding: .45rem .75rem;
            border:
                1px solid
                rgb(203 213 225);
            border-radius: .5rem;
            background: transparent;
            font-weight: 700;
            cursor: pointer;
        }

        .dark .rs-reset {
            border-color:
                rgb(71 85 105);
        }

        .rs-table-wrap {
            overflow-x: auto;
        }

        .rs-table {
            width: 100%;
            border-collapse:
                collapse;
            font-size: .86rem;
        }

        .rs-table th,
        .rs-table td {
            padding: .72rem .8rem;
            border-bottom:
                1px solid
                rgb(241 245 249);
            text-align: left;
            vertical-align: top;
        }

        .dark .rs-table th,
        .dark .rs-table td {
            border-bottom-color:
                rgb(30 41 59);
        }

        .rs-table th {
            color:
                rgb(71 85 105);
            background:
                rgb(248 250 252);
            font-size: .75rem;
            text-transform:
                uppercase;
            letter-spacing: .03em;
        }

        .dark .rs-table th {
            color:
                rgb(203 213 225);
            background:
                rgb(30 41 59);
        }

        .rs-badge {
            display: inline-flex;
            padding: .2rem .45rem;
            border-radius: 9999px;
            background:
                rgb(239 246 255);
            color:
                rgb(29 78 216);
            font-size: .74rem;
            font-weight: 800;
        }

        .dark .rs-badge {
            background:
                rgb(30 58 138);
            color:
                rgb(219 234 254);
        }

        .rs-empty {
            padding: 2rem 1rem;
            color:
                rgb(100 116 139);
            text-align: center;
        }

        .rs-info {
            padding: .9rem 1rem;
            border:
                1px solid
                rgb(191 219 254);
            border-radius: .7rem;
            margin-bottom: 1.25rem;
            background:
                rgb(239 246 255);
            color:
                rgb(30 64 175);
            line-height: 1.45;
            font-size: .88rem;
        }

        .dark .rs-info {
            border-color:
                rgb(30 64 175);
            background:
                rgb(23 37 84);
            color:
                rgb(219 234 254);
        }

        @media (
            max-width: 1100px
        ) {
            .rs-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }
        }

        @media (
            max-width: 650px
        ) {
            .rs-grid {
                grid-template-columns:
                    1fr;
            }

            .rs-panel-head {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>

    @php
        $summary =
            $this->summary();

        $master =
            $summary['master']
            ?? [];

        $occupations =
            $this->occupations();

        $occupationCount =
            $this
                ->filteredOccupationsCount();

        $sessions =
            $this
                ->skeletorSessions();

        $rooms =
            $this
                ->roomOptions();
    @endphp

    <div class="rs-info">
        <strong>
            Principe :
        </strong>
        le fichier Excel / SharePoint reste le fichier maître.
        Les réservations importées bloquent les salles dans Skeletor.
        L’export repart de ce fichier, conserve les occupations existantes
        et ajoute les sessions planifiées par Skeletor.
    </div>

    <div class="rs-grid">
        <div class="rs-card">
            <div class="rs-card-label">
                Fichier maître
            </div>
            <div
                class="rs-card-value"
                style="font-size:1rem"
            >
                {{
                    $master['original_name']
                    ?? 'Aucun'
                }}
            </div>
            <div class="rs-card-small">
                @if (
                    ! empty(
                        $master['imported_at']
                    )
                )
                    Importé le
                    {{
                        \Carbon\Carbon::parse(
                            $master['imported_at']
                        )->format(
                            'd/m/Y H:i'
                        )
                    }}
                @else
                    Aucun import enregistré.
                @endif
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-card-label">
                Année
            </div>
            <div class="rs-card-value">
                {{
                    $master['year']
                    ?? '—'
                }}
            </div>
            <div class="rs-card-small">
                Calendrier Excel actif.
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-card-label">
                Salles actives
            </div>
            <div class="rs-card-value">
                {{
                    $summary['salles']
                }}
            </div>
            <div class="rs-card-small">
                {{
                    $summary['sans_capacite']
                }}
                sans capacité renseignée.
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-card-label">
                Occupations Excel
            </div>
            <div class="rs-card-value">
                {{
                    $summary[
                        'occupations_excel'
                    ]
                }}
            </div>
            <div class="rs-card-small">
                Créneaux bloquants importés.
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-card-label">
                Sessions Skeletor
            </div>
            <div class="rs-card-value">
                {{
                    $summary[
                        'sessions_skeletor'
                    ]
                }}
            </div>
            <div class="rs-card-small">
                Sessions avec une salle.
            </div>
        </div>
    </div>

    <section class="rs-panel">
        <div class="rs-panel-head">
            <div>
                <div class="rs-panel-title">
                    Occupations importées depuis Excel
                </div>
                <div class="rs-panel-subtitle">
                    {{
                        $occupationCount
                    }}
                    résultat(s).
                    Les 250 premiers sont affichés.
                </div>
            </div>

            <div class="rs-filters">
                <div class="rs-field">
                    <label>
                        Salle
                    </label>
                    <select
                        wire:model.live="salleFilter"
                    >
                        <option value="">
                            Toutes
                        </option>

                        @foreach (
                            $rooms
                            as $room
                        )
                            <option
                                value="{{ $room->id }}"
                            >
                                {{
                                    $room->code
                                    ? $room->code
                                        . ' — '
                                        . $room->nom
                                    : $room->nom
                                }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rs-field">
                    <label>
                        Date
                    </label>
                    <input
                        type="date"
                        wire:model.live="dateFilter"
                    >
                </div>

                <button
                    type="button"
                    class="rs-reset"
                    wire:click="resetFilters"
                >
                    Réinitialiser
                </button>
            </div>
        </div>

        <div class="rs-table-wrap">
            @if (
                $occupations
                    ->isEmpty()
            )
                <div class="rs-empty">
                    Aucune occupation ne correspond aux filtres.
                </div>
            @else
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th>Salle</th>
                            <th>Date</th>
                            <th>Horaire</th>
                            <th>Libellé</th>
                            <th>Origine Excel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (
                            $occupations
                            as $occupation
                        )
                            <tr>
                                <td>
                                    <strong>
                                        {{
                                            $occupation
                                                ->salle
                                                ?->code
                                            ?: $occupation
                                                ->salle
                                                ?->nom
                                            ?: '—'
                                        }}
                                    </strong>
                                </td>

                                <td>
                                    {{
                                        $occupation
                                            ->debut
                                            ?->format(
                                                'd/m/Y'
                                            )
                                        ?? '—'
                                    }}
                                </td>

                                <td>
                                    {{
                                        $occupation
                                            ->debut
                                            ?->format(
                                                'H:i'
                                            )
                                        ?? '—'
                                    }}
                                    →
                                    {{
                                        $occupation
                                            ->fin
                                            ?->format(
                                                'H:i'
                                            )
                                        ?? '—'
                                    }}
                                </td>

                                <td>
                                    {{
                                        $occupation
                                            ->libelle
                                    }}
                                </td>

                                <td>
                                    <span class="rs-badge">
                                        {{
                                            $occupation
                                                ->excel_range
                                            ?: 'Excel'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="rs-panel">
        <div class="rs-panel-head">
            <div>
                <div class="rs-panel-title">
                    Sessions Skeletor à réinjecter à l’export
                </div>
                <div class="rs-panel-subtitle">
                    Aperçu des sessions planifiées ou confirmées
                    ayant une salle.
                </div>
            </div>
        </div>

        <div class="rs-table-wrap">
            @if (
                $sessions
                    ->isEmpty()
            )
                <div class="rs-empty">
                    Aucune session Skeletor avec salle pour le moment.
                </div>
            @else
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th>Stage</th>
                            <th>Salle</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Capacité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (
                            $sessions
                            as $session
                        )
                            <tr>
                                <td>
                                    <strong>
                                        {{
                                            $session
                                                ->stage
                                                ?->libelle_court
                                            ?? $session
                                                ->code_session
                                        }}
                                    </strong>
                                </td>

                                <td>
                                    {{
                                        $session
                                            ->salle
                                            ?->code
                                        ?: $session
                                            ->salle
                                            ?->nom
                                        ?: '—'
                                    }}
                                </td>

                                <td>
                                    {{
                                        $session
                                            ->debut
                                            ?->format(
                                                'd/m/Y H:i'
                                            )
                                        ?? '—'
                                    }}
                                </td>

                                <td>
                                    {{
                                        $session
                                            ->fin
                                            ?->format(
                                                'd/m/Y H:i'
                                            )
                                        ?? '—'
                                    }}
                                </td>

                                <td>
                                    {{
                                        $session
                                            ->capacite_max
                                        ?? '—'
                                    }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>
</x-filament-panels::page>

