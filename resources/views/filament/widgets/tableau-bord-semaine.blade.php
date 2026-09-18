<x-filament-widgets::widget>

    @php
        $data = $this->dashboardData();
    @endphp

    <style>
        .ps-dashboard {
            width: 100%;
        }

        .ps-dashboard-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .ps-toolbar-title {
            font-size: 1.15rem;
            font-weight: 750;
        }

        .ps-dashboard-toolbar-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .ps-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .65rem 1rem;
            border-radius: .6rem;
            border: 1px solid #d1d5db;
            text-decoration: none;
            font-weight: 650;
            background: white;
            color: #111827;
            cursor: pointer;
        }

        .ps-button:hover {
            background: #f8fafc;
        }

        .ps-button-primary {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .ps-summary {
            display: grid;
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .ps-stat {
            padding: 1.2rem;
            border-radius: .8rem;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .ps-stat-action {
            border-left: 5px solid #f59e0b;
        }

        .ps-stat-value {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .ps-stat-label {
            margin-top: .3rem;
            color: #6b7280;
            font-size: .9rem;
        }

        .ps-section-title {
            margin: 1.5rem 0 .75rem;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .ps-candidates {
            overflow: hidden;
            margin-bottom: 1.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
        }

        .ps-candidate {
            display: grid;
            grid-template-columns:
                minmax(180px, 1.2fr)
                minmax(180px, 1.5fr)
                minmax(160px, 1fr)
                180px
                100px;
            gap: 1rem;
            align-items: center;
            padding: .8rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .ps-candidate:last-child {
            border-bottom: none;
        }

        .ps-candidate-name {
            font-weight: 750;
        }

        .ps-candidate-small {
            color: #64748b;
            font-size: .82rem;
        }

        .ps-status {
            display: inline-flex;
            width: fit-content;
            padding: .3rem .55rem;
            border-radius: 9999px;
            font-size: .78rem;
            font-weight: 750;
        }

        .ps-status-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .ps-status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .ps-status-neutral {
            background: #e2e8f0;
            color: #334155;
        }

        .ps-open {
            display: inline-flex;
            justify-content: center;
            padding: .45rem .65rem;
            border-radius: .5rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: .8rem;
            font-weight: 750;
        }

        .ps-empty-candidates {
            padding: 1rem;
            color: #15803d;
            font-weight: 650;
        }

        .ps-week {
            display: grid;
            gap: 1rem;
        }

        .ps-day {
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
            background: white;
            overflow: hidden;
        }

        .ps-day-header {
            padding: .8rem 1rem;
            font-weight: 750;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .ps-day-today .ps-day-header {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .ps-event {
            display: grid;
            grid-template-columns:
                110px
                minmax(0, 2fr)
                minmax(140px, 1fr)
                150px;
            gap: 1rem;
            align-items: center;
            padding: .9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: inherit;
            text-decoration: none;
        }

        .ps-event:hover {
            background: #f8fafc;
        }

        .ps-event:last-child {
            border-bottom: 0;
        }

        .ps-event-time {
            font-weight: 750;
        }

        .ps-event-title {
            font-weight: 750;
        }

        .ps-event-code {
            color: #64748b;
            font-size: .85rem;
        }

        .ps-event-room {
            color: #475569;
        }

        .ps-capacity {
            text-align: right;
            font-weight: 750;
        }

        .ps-empty {
            padding: 1rem;
            color: #94a3b8;
        }

        .ps-quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: .7rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 1200px) {
            .ps-summary {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

            .ps-candidate {
                grid-template-columns:
                    1fr
                    1fr
                    180px;
            }
        }

        @media (max-width: 800px) {
            .ps-summary {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .ps-candidate {
                grid-template-columns: 1fr;
            }

            .ps-event {
                grid-template-columns:
                    90px
                    minmax(0, 1fr);
            }

            .ps-event-room,
            .ps-capacity {
                grid-column: 2;
                text-align: left;
            }
        }

        @media (max-width: 550px) {
            .ps-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="ps-dashboard">

        <div class="ps-dashboard-toolbar">

            <div class="ps-toolbar-title">
                {{ $data['week_label'] }}
            </div>

            <div class="ps-dashboard-toolbar-actions">

                <button
                    type="button"
                    class="ps-button"
                    wire:click="previousWeek"
                >
                    ← Semaine précédente
                </button>

                <button
                    type="button"
                    class="ps-button"
                    wire:click="goToCurrentWeek"
                >
                    Cette semaine
                </button>

                <button
                    type="button"
                    class="ps-button"
                    wire:click="nextWeek"
                >
                    Semaine suivante →
                </button>

            </div>

        </div>

        <div class="ps-summary">

            <div class="ps-stat">
                <div class="ps-stat-value">
                    {{ $data['sessions_count'] }}
                </div>

                <div class="ps-stat-label">
                    Sessions cette semaine
                </div>
            </div>

            <div class="ps-stat">
                <div class="ps-stat-value">
                    {{ $data['participants_count'] }}
                </div>

                <div class="ps-stat-label">
                    Stagiaires inscrits
                </div>
            </div>

            <div class="ps-stat">
                <div class="ps-stat-value">
                    {{ $data['places_restantes'] }}
                </div>

                <div class="ps-stat-label">
                    Places restantes
                </div>
            </div>

            <a
                href="{{ $data['urls']['besoins'] }}"
                class="ps-stat"
                style="text-decoration: none; color: inherit; cursor: pointer;"
                title="Voir les besoins à planifier"
            >
                <div class="ps-stat-value">
                    {{ $data['besoins_a_planifier'] }}
                </div>

                <div class="ps-stat-label">
                    Besoins à planifier
                </div>
            </a>

            <a
                href="{{ $data['urls']['inscriptions'] }}"
                class="ps-stat ps-stat-action"
                style="text-decoration: none; color: inherit; cursor: pointer;"
                title="Voir les candidatures à traiter"
            >
                <div class="ps-stat-value">
                    {{ $data['candidatures_a_traiter'] }}
                </div>

                <div class="ps-stat-label">
                    Candidatures à traiter
                </div>
            </a>

        </div>

        <div class="ps-section-title">
            Candidatures à traiter
        </div>

        <div class="ps-candidates">

            @forelse ($data['candidatures'] as $candidature)

                <div class="ps-candidate">

                    <div>
                        <div class="ps-candidate-name">
                            {{ $candidature['nom'] }}
                        </div>

                        <div class="ps-candidate-small">
                            {{ $candidature['code'] }}
                        </div>

                        <div class="ps-candidate-small">
                            {{ $candidature['email'] }}
                        </div>
                    </div>

                    <div>
                        <strong>
                            {{ $candidature['stage'] }}
                        </strong>

                        <div class="ps-candidate-small">
                            {{ $candidature['session'] }}
                        </div>
                    </div>

                    <div>
                        <span
                            class="
                                ps-status
                                ps-status-{{ $candidature['statut_class'] }}
                            "
                        >
                            {{ $candidature['statut_label'] }}
                        </span>
                    </div>

                    <div></div>

                    <div>
                        <a
                            href="{{ $candidature['url'] }}"
                            class="ps-open"
                        >
                            Ouvrir
                        </a>
                    </div>

                </div>

            @empty

                <div class="ps-empty-candidates">
                    ✓ Aucune candidature en attente de traitement.
                </div>

            @endforelse

        </div>

        <div class="ps-section-title">
            Planning de la semaine
        </div>

        <div class="ps-week">

            @foreach ($data['days'] as $day)

                <div
                    class="
                        ps-day
                        {{ $day['is_today'] ? 'ps-day-today' : '' }}
                    "
                >

                    <div class="ps-day-header">

                        {{ $day['label'] }}

                        @if ($day['is_today'])
                            — Aujourd’hui
                        @endif

                    </div>

                    @forelse ($day['events'] as $event)

                        <a
                            href="{{ $event['url'] }}"
                            class="ps-event"
                        >

                            <div class="ps-event-time">
                                {{ $event['debut'] }}
                                →
                                {{ $event['fin'] }}
                            </div>

                            <div>
                                <div class="ps-event-title">
                                    {{ $event['stage'] }}
                                </div>

                                <div class="ps-event-code">
                                    {{ $event['code'] }}
                                </div>
                            </div>

                            <div class="ps-event-room">

                                @if ($event['salle'])
                                    {{ $event['salle'] }}
                                @else
                                    Salle non affectée
                                @endif

                            </div>

                            <div class="ps-capacity">

                                @if ($event['capacite'] !== null)

                                    {{ $event['participants'] }}
                                    /
                                    {{ $event['capacite'] }}
                                    stagiaires

                                @else

                                    {{ $event['participants'] }}
                                    stagiaire(s)

                                @endif

                            </div>

                        </a>

                    @empty

                        <div class="ps-empty">
                            Aucun stage prévu.
                        </div>

                    @endforelse

                </div>

            @endforeach

        </div>

        <div class="ps-quick-links">

            <a
                href="{{ $data['urls']['planning'] }}"
                class="ps-button ps-button-primary"
            >
                Planning complet
            </a>

            <a
                href="{{ $data['urls']['besoins'] }}"
                class="ps-button"
            >
                Besoins
            </a>

            <a
                href="{{ $data['urls']['inscriptions'] }}"
                class="ps-button"
            >
                Toutes les inscriptions
            </a>

            <a
                href="{{ $data['urls']['statistiques'] }}"
                class="ps-button"
            >
                Statistiques
            </a>

        </div>

    </div>

</x-filament-widgets::widget>
