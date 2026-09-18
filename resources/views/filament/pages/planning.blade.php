<x-filament-panels::page>

    @php
        $monthCalendar = $this->mode === 'month'
            ? $this->calendarData()
            : null;

        $weekCalendar = $this->mode === 'week'
            ? $this->weekData()
            : null;

        $filters = $this->filterOptions();

        $weekDays = [
            'Lun',
            'Mar',
            'Mer',
            'Jeu',
            'Ven',
        ];

        $hours = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
        ];
    @endphp

    <style>
        .planning-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .planning-toolbar-group {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .planning-period-title {
            min-width: 260px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .planning-button {
            border: 1px solid #d1d5db;
            border-radius: .55rem;
            padding: .55rem .9rem;
            background: white;
            cursor: pointer;
            font-weight: 600;
        }

        .planning-button:hover {
            background: #f3f4f6;
        }

        .planning-button.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .planning-filters {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(180px, 1fr))
                auto;
            gap: .75rem;
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: white;
        }

        .planning-filter-field label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            margin-bottom: .3rem;
        }

        .planning-filter-field select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            padding: .55rem .7rem;
            background: white;
        }

        .planning-filter-reset {
            display: flex;
            align-items: end;
        }

        .planning-scroll {
            overflow-x: auto;
        }

        /* VUE MOIS */

        .planning-calendar {
            min-width: 1180px;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            overflow: hidden;
            background: white;
        }

        .planning-weekdays {
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .planning-weekday {
            padding: .7rem;
            text-align: center;
            font-weight: 700;
            color: #475569;
        }

        .planning-week-label-header {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
            border-right: 1px solid #e5e7eb;
        }

        .planning-week-row {
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
        }

        .planning-week-number {
            min-height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
        }

        .planning-week-number span {
            border-radius: .45rem;
            padding: .35rem .45rem;
            background: white;
            border: 1px solid #e2e8f0;
        }

        .planning-day {
            min-height: 170px;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: .45rem;
            background: white;
            cursor: pointer;
        }

        .planning-day:hover {
            background: #f8fbff;
        }

        .planning-week-row .planning-day:last-child {
            border-right: none;
        }

        .planning-day.outside-month {
            background: #f8fafc;
        }

        .planning-day-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .35rem;
            margin-bottom: .4rem;
        }

        .planning-day-number {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-radius: 9999px;
        }

        .outside-month .planning-day-number {
            color: #94a3b8;
        }

        .planning-day-number.today {
            background: #2563eb;
            color: white;
        }

        .planning-add {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.8rem;
            height: 1.8rem;
            border-radius: .45rem;
            color: #64748b;
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: none;
            opacity: .55;
        }

        .planning-day:hover .planning-add {
            opacity: 1;
            background: #eff6ff;
            color: #2563eb;
        }

        /* ÉVÉNEMENTS */

        .planning-event {
            display: block;
            text-decoration: none;
            border-radius: .5rem;
            padding: .45rem .5rem;
            margin-bottom: .4rem;
            border-left: 4px solid;
            font-size: .78rem;
            line-height: 1.25;
            overflow: hidden;
        }

        .planning-event-title {
            font-weight: 700;
            margin-bottom: .15rem;
        }

        .planning-event-info {
            opacity: .82;
            margin-top: .1rem;
        }

        .status-brouillon {
            background: #f1f5f9;
            border-color: #64748b;
            color: #334155;
        }

        .status-planifiee {
            background: #eff6ff;
            border-color: #2563eb;
            color: #1e3a8a;
        }

        .status-confirmee {
            background: #ecfdf5;
            border-color: #16a34a;
            color: #14532d;
        }

        .status-annulee {
            background: #fef2f2;
            border-color: #dc2626;
            color: #7f1d1d;
            opacity: .7;
        }

        .status-terminee {
            background: #f5f3ff;
            border-color: #7c3aed;
            color: #4c1d95;
        }

        /* VUE SEMAINE */

        .week-calendar {
            min-width: 1250px;
            display: grid;
            grid-template-columns:
                72px
                repeat(5, minmax(150px, 1fr));
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            overflow: hidden;
            background: white;
        }

        .week-time-header,
        .week-day-header {
            height: 56px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .week-time-header {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
        }

        .week-day-header {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-left: 1px solid #e5e7eb;
        }

        .week-day-header.today {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .week-time-body,
        .week-day-body {
            height: 720px;
            position: relative;
        }

        .week-time-body {
            border-right: 1px solid #e5e7eb;
        }

        .week-hour-label {
            position: absolute;
            right: .55rem;
            transform: translateY(-50%);
            font-size: .75rem;
            color: #64748b;
        }

        .week-day-body {
            border-left: 1px solid #e5e7eb;
            position: relative;
        }

        .week-day-body.today {
            background: rgba(37, 99, 235, .025);
        }

        .week-hour-line {
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px solid #e5e7eb;
            pointer-events: none;
            z-index: 0;
        }

        /*
         * Zone cliquable correspondant
         * à chaque heure.
         */
        .week-click-slot {
            position: absolute;
            left: 0;
            right: 0;
            display: block;
            z-index: 1;
            text-decoration: none;
            border-radius: .25rem;
        }

        .week-click-slot:hover {
            background: rgba(37, 99, 235, .07);
        }

        .week-click-slot::after {
            content: attr(data-label);
            position: absolute;
            top: .3rem;
            right: .35rem;
            padding: .15rem .35rem;
            border-radius: .35rem;
            background: #2563eb;
            color: white;
            font-size: .68rem;
            font-weight: 700;
            opacity: 0;
            transition: opacity .1s ease;
            pointer-events: none;
        }

        .week-click-slot:hover::after {
            opacity: 1;
        }

        .week-event {
            position: absolute;
            display: block;
            box-sizing: border-box;
            padding: .35rem .4rem;
            border-radius: .45rem;
            border-left: 4px solid;
            overflow: hidden;
            text-decoration: none;
            font-size: .72rem;
            line-height: 1.2;
            z-index: 5;
        }

        .week-event:hover {
            z-index: 6;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .12);
        }

        .week-event-title {
            font-weight: 700;
        }

        .week-event-info {
            margin-top: .12rem;
            opacity: .82;
        }

        /* LÉGENDE */

        .planning-legend {
            display: flex;
            gap: .8rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            font-size: .82rem;
        }

        .planning-legend-item {
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .planning-legend-dot {
            width: .75rem;
            height: .75rem;
            border-radius: 9999px;
        }

        .dot-brouillon {
            background: #64748b;
        }

        .dot-planifiee {
            background: #2563eb;
        }

        .dot-confirmee {
            background: #16a34a;
        }

        .dot-annulee {
            background: #dc2626;
        }

        .dot-terminee {
            background: #7c3aed;
        }

        @media (max-width: 1100px) {
            .planning-filters {
                grid-template-columns:
                    repeat(2, minmax(180px, 1fr));
            }
        }
    </style>

    <div class="planning-toolbar">

        <div class="planning-toolbar-group">

            <button
                type="button"
                class="planning-button {{ $this->mode === 'month' ? 'active' : '' }}"
                wire:click="setMode('month')"
            >
                Mois
            </button>

            <button
                type="button"
                class="planning-button {{ $this->mode === 'week' ? 'active' : '' }}"
                wire:click="setMode('week')"
            >
                Semaine
            </button>

        </div>

        <div class="planning-toolbar-group">

            <button
                type="button"
                class="planning-button"
                wire:click="previousPeriod"
            >
                ←
            </button>

            <div class="planning-period-title">
                @if ($this->mode === 'month')
                    {{ $monthCalendar['label'] }}
                @else
                    {{ $weekCalendar['label'] }}
                @endif
            </div>

            <button
                type="button"
                class="planning-button"
                wire:click="nextPeriod"
            >
                →
            </button>

        </div>

        <div class="planning-toolbar-group">

            <button
                type="button"
                class="planning-button"
                wire:click="goToToday"
            >
                Aujourd'hui
            </button>

        </div>

    </div>

    <div class="planning-filters">

        <div class="planning-filter-field">
            <label>Stage</label>

            <select wire:model.live="stageFilter">
                <option value="">
                    Tous les stages
                </option>

                @foreach ($filters['stages'] as $stage)
                    <option value="{{ $stage['id'] }}">
                        {{ $stage['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Instructeur</label>

            <select wire:model.live="instructeurFilter">
                <option value="">
                    Tous les instructeurs
                </option>

                @foreach ($filters['instructeurs'] as $instructeur)
                    <option value="{{ $instructeur['id'] }}">
                        {{ $instructeur['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Salle</label>

            <select wire:model.live="salleFilter">
                <option value="">
                    Toutes les salles
                </option>

                @foreach ($filters['salles'] as $salle)
                    <option value="{{ $salle['id'] }}">
                        {{ $salle['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Statut</label>

            <select wire:model.live="statutFilter">
                <option value="">
                    Tous les statuts
                </option>

                <option value="brouillon">
                    Brouillon
                </option>

                <option value="planifiee">
                    Planifiée
                </option>

                <option value="confirmee">
                    Confirmée
                </option>

                <option value="annulee">
                    Annulée
                </option>

                <option value="terminee">
                    Terminée
                </option>
            </select>
        </div>

        <div class="planning-filter-reset">

            <button
                type="button"
                class="planning-button"
                wire:click="resetFilters"
            >
                Réinitialiser
            </button>

        </div>

    </div>

    @if ($this->mode === 'month')

        <div class="planning-scroll">

            <div class="planning-calendar">

                <div class="planning-weekdays">

                    <div class="planning-week-label-header">
                        Sem.
                    </div>

                    @foreach ($weekDays as $weekDay)

                        <div class="planning-weekday">
                            {{ $weekDay }}
                        </div>

                    @endforeach

                </div>

                @foreach (
                    array_chunk(
                        array_values(
                            array_filter(
                                $monthCalendar['days'],
                                fn ($day) =>
                                    \Carbon\Carbon::parse(
                                        $day['date']
                                    )->isWeekday()
                            )
                        ),
                        5
                    )
                    as $week
                )

                    <div class="planning-week-row">

                        <div class="planning-week-number">
                            <span>
                                S{{ \Carbon\Carbon::parse($week[0]['date'])->isoWeek() }}
                            </span>
                        </div>

                        @foreach ($week as $day)

                            <div
                                class="
                                    planning-day
                                    {{ $day['is_current_month'] ? '' : 'outside-month' }}
                                "
                                onclick="window.location.href='{{ $day['create_url'] }}'"
                            >

                                <div class="planning-day-top">

                                    <div
                                        class="
                                            planning-day-number
                                            {{ $day['is_today'] ? 'today' : '' }}
                                        "
                                    >
                                        {{ $day['day'] }}
                                    </div>

                                    <a
                                        href="{{ $day['create_url'] }}"
                                        class="planning-add"
                                        onclick="event.stopPropagation();"
                                    >
                                        +
                                    </a>

                                </div>

                                @foreach ($day['events'] as $event)

                                    <a
                                        href="{{ $event['url'] }}"
                                        class="
                                            planning-event
                                            status-{{ $event['statut'] }}
                                        "
                                        onclick="event.stopPropagation();"
                                    >

                                        <div class="planning-event-title">
                                            {{ $event['stage'] }}
                                        </div>

                                        <div class="planning-event-info">
                                            {{ $event['periode'] }}
                                        </div>

                                        @if ($event['salle'])
                                            <div class="planning-event-info">
                                                🏢 {{ $event['salle'] }}
                                            </div>
                                        @endif

                                        @if ($event['instructeurs'])
                                            <div class="planning-event-info">
                                                👤 {{ $event['instructeurs'] }}
                                            </div>
                                        @endif

                                    </a>

                                @endforeach

                            </div>

                        @endforeach

                    </div>

                @endforeach

            </div>

        </div>

    @else

        <div class="planning-scroll">

            <div class="week-calendar">

                <div class="week-time-header">
                    S{{ \Carbon\Carbon::parse($this->currentDate)->isoWeek() }}
                </div>

                @foreach ($weekCalendar['days'] as $day)

                    <div
                        class="
                            week-day-header
                            {{ $day['is_today'] ? 'today' : '' }}
                        "
                    >
                        {{ $day['label'] }}
                    </div>

                @endforeach

                <div class="week-time-body">

                    @foreach ($hours as $index => $hour)

                        <div
                            class="week-hour-label"
                            style="top: {{ ($index / 8) * 100 }}%;"
                        >
                            {{ $hour }}
                        </div>

                    @endforeach

                </div>

                @foreach ($weekCalendar['days'] as $day)

                    <div
                        class="
                            week-day-body
                            {{ $day['is_today'] ? 'today' : '' }}
                        "
                    >

                        @for ($i = 0; $i <= 8; $i++)

                            <div
                                class="week-hour-line"
                                style="top: {{ ($i / 8) * 100 }}%;"
                            ></div>

                        @endfor

                        @for ($hour = 8; $hour <= 15; $hour++)

                            @php
                                $heureLabel = sprintf(
                                    '%02d:00',
                                    $hour
                                );

                                $slotIndex =
                                    $hour - 8;
                            @endphp

                            <a
                                href="{{ $day['create_urls'][$heureLabel] }}"
                                class="week-click-slot"
                                data-label="+ {{ $heureLabel }}"
                                style="
                                    top: {{ ($slotIndex / 8) * 100 }}%;
                                    height: 12.5%;
                                "
                                title="Créer une session le {{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }} à {{ $heureLabel }}"
                            ></a>

                        @endfor

                        @foreach ($day['events'] as $event)

                            <a
                                href="{{ $event['url'] }}"
                                class="
                                    week-event
                                    status-{{ $event['statut'] }}
                                "
                                style="
                                    top: {{ $event['top'] }}%;
                                    height: {{ max($event['height'], 5) }}%;
                                    left: calc({{ $event['left'] }}% + 2px);
                                    width: calc({{ $event['width'] }}% - 4px);
                                "
                                title="{{ $event['code'] }}"
                            >

                                <div class="week-event-title">
                                    {{ $event['stage'] }}
                                </div>

                                <div class="week-event-info">
                                    {{ $event['periode'] }}
                                </div>

                                @if ($event['salle'])
                                    <div class="week-event-info">
                                        🏢 {{ $event['salle'] }}
                                    </div>
                                @endif

                                @if ($event['instructeurs'])
                                    <div class="week-event-info">
                                        👤 {{ $event['instructeurs'] }}
                                    </div>
                                @endif

                            </a>

                        @endforeach

                    </div>

                @endforeach

            </div>

        </div>

    @endif

    <div class="planning-legend">

        <div class="planning-legend-item">
            <span class="planning-legend-dot dot-brouillon"></span>
            Brouillon
        </div>

        <div class="planning-legend-item">
            <span class="planning-legend-dot dot-planifiee"></span>
            Planifiée
        </div>

        <div class="planning-legend-item">
            <span class="planning-legend-dot dot-confirmee"></span>
            Confirmée
        </div>

        <div class="planning-legend-item">
            <span class="planning-legend-dot dot-annulee"></span>
            Annulée
        </div>

        <div class="planning-legend-item">
            <span class="planning-legend-dot dot-terminee"></span>
            Terminée
        </div>

    </div>

</x-filament-panels::page>