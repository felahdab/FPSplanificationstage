<x-filament-panels::page>

    @php
        $stats = $this->statsData();
        $years = $this->availableYears();
    @endphp

    <style>
        .ps-stats-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .ps-stats-year-title {
            font-size: 1.25rem;
            font-weight: 800;
        }

        .ps-stats-year-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .ps-stats-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: .6rem .9rem;
            border-radius: .6rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #111827;
            font-weight: 650;
            cursor: pointer;
        }

        .ps-stats-button:hover {
            background: #f8fafc;
        }

        .ps-stats-select {
            min-height: 40px;
            padding: .55rem 2rem .55rem .75rem;
            border: 1px solid #d1d5db;
            border-radius: .6rem;
            background: white;
            color: #111827;
            font-weight: 700;
        }

        .ps-section-title {
            margin: 1.8rem 0 .8rem;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .ps-stats-grid {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .ps-stats-card {
            padding: 1.25rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
        }

        .ps-stats-card-primary {
            border-left: 5px solid #2563eb;
        }

        .ps-stats-card-success {
            border-left: 5px solid #16a34a;
        }

        .ps-stats-card-warning {
            border-left: 5px solid #f59e0b;
        }

        .ps-stats-card-danger {
            border-left: 5px solid #dc2626;
        }

        .ps-stats-card-neutral {
            border-left: 5px solid #64748b;
        }

        .ps-stats-value {
            font-size: 2rem;
            font-weight: 800;
        }

        .ps-stats-label {
            margin-top: .35rem;
            color: #64748b;
        }

        .ps-stats-note {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: .8rem;
            background: #eff6ff;
            color: #1e40af;
        }

        @media (max-width: 1100px) {
            .ps-stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .ps-stats-grid {
                grid-template-columns: 1fr;
            }

            .ps-stats-year-actions {
                width: 100%;
            }
        }
    </style>

    <div class="ps-stats-toolbar">

        <div class="ps-stats-year-title">
            Statistiques de l'année
            {{ $stats['year'] }}
        </div>

        <div class="ps-stats-year-actions">

            <button
                type="button"
                class="ps-stats-button"
                wire:click="previousYear"
            >
                ← Année précédente
            </button>

            <select
                class="ps-stats-select"
                wire:model.live="selectedYear"
            >

                @foreach ($years as $year)

                    <option
                        value="{{ $year }}"
                    >
                        {{ $year }}
                    </option>

                @endforeach

            </select>

            <button
                type="button"
                class="ps-stats-button"
                wire:click="currentYear"
            >
                Année actuelle
            </button>

            <button
                type="button"
                class="ps-stats-button"
                wire:click="nextYear"
            >
                Année suivante →
            </button>

        </div>

    </div>

    <div class="ps-section-title">
        Sessions
    </div>

    <div class="ps-stats-grid">

        <div class="
            ps-stats-card
            ps-stats-card-primary
        ">
            <div class="ps-stats-value">
                {{ $stats['sessions_programmees'] }}
            </div>

            <div class="ps-stats-label">
                Sessions programmées en
                {{ $stats['year'] }}
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-success
        ">
            <div class="ps-stats-value">
                {{ $stats['sessions_realisees'] }}
            </div>

            <div class="ps-stats-label">
                Sessions réalisées
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-primary
        ">
            <div class="ps-stats-value">
                {{ $stats['sessions_a_venir'] }}
            </div>

            <div class="ps-stats-label">
                Sessions à venir
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-danger
        ">
            <div class="ps-stats-value">
                {{ $stats['sessions_annulees'] }}
            </div>

            <div class="ps-stats-label">
                Sessions annulées
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-warning
        ">
            <div class="ps-stats-value">
                {{ $stats['sessions_en_cours'] }}
            </div>

            <div class="ps-stats-label">
                Sessions actuellement en cours
            </div>
        </div>

    </div>

    <div class="ps-section-title">
        Stagiaires et remplissage
    </div>

    <div class="ps-stats-grid">

        <div class="
            ps-stats-card
            ps-stats-card-primary
        ">
            <div class="ps-stats-value">
                {{ $stats['stagiaires_reserves'] }}
            </div>

            <div class="ps-stats-label">
                Places réservées
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-success
        ">
            <div class="ps-stats-value">
                {{ $stats['stagiaires_confirmes'] }}
            </div>

            <div class="ps-stats-label">
                Stagiaires confirmés
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-warning
        ">
            <div class="ps-stats-value">
                {{ $stats['liste_attente'] }}
            </div>

            <div class="ps-stats-label">
                En liste d'attente
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-neutral
        ">
            <div class="ps-stats-value">
                {{ $stats['capacite_totale'] }}
            </div>

            <div class="ps-stats-label">
                Capacité totale programmée
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-primary
        ">
            <div class="ps-stats-value">
                {{ $stats['places_occupees'] }}
            </div>

            <div class="ps-stats-label">
                Places occupées
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-neutral
        ">
            <div class="ps-stats-value">
                {{ $stats['places_restantes'] }}
            </div>

            <div class="ps-stats-label">
                Places encore disponibles
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-success
        ">
            <div class="ps-stats-value">
                {{ $stats['taux_remplissage'] }} %
            </div>

            <div class="ps-stats-label">
                Taux de remplissage
            </div>
        </div>

    </div>

    <div class="ps-section-title">
        Besoins de formation
    </div>

    <div class="ps-stats-grid">

        <div class="
            ps-stats-card
            ps-stats-card-neutral
        ">
            <div class="ps-stats-value">
                {{ $stats['besoins_total'] }}
            </div>

            <div class="ps-stats-label">
                Besoins reçus en
                {{ $stats['year'] }}
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-warning
        ">
            <div class="ps-stats-value">
                {{ $stats['besoins_a_planifier'] }}
            </div>

            <div class="ps-stats-label">
                Besoins à planifier
            </div>
        </div>

        <div class="
            ps-stats-card
            ps-stats-card-success
        ">
            <div class="ps-stats-value">
                {{ $stats['besoins_planifies'] }}
            </div>

            <div class="ps-stats-label">
                Besoins planifiés
            </div>
        </div>

    </div>

    <div class="ps-stats-note">
        Les statistiques sont calculées pour l'année
        sélectionnée. Une session est considérée comme
        réalisée lorsque sa date de fin est passée et
        qu'elle n'est pas annulée.
    </div>

</x-filament-panels::page>