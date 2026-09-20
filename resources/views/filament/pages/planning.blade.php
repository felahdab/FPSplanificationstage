<x-filament-panels::page>
    @php
        $filters = $this->filterOptions();
    @endphp

    <style>
        .planning-filters {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr)) auto;
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

        .planning-button {
            border: 1px solid #d1d5db;
            border-radius: .55rem;
            padding: .55rem .9rem;
            background: white;
            cursor: pointer;
            font-weight: 600;
        }

        @media (max-width: 1100px) {
            .planning-filters {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }
    </style>

    <div class="planning-filters">
        <div class="planning-filter-field">
            <label>Stage</label>
            <select wire:model.live="stageFilter">
                <option value="">Tous les stages</option>
                @foreach ($filters['stages'] as $stage)
                    <option value="{{ $stage['id'] }}">{{ $stage['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Instructeur</label>
            <select wire:model.live="instructeurFilter">
                <option value="">Tous les instructeurs</option>
                @foreach ($filters['instructeurs'] as $instructeur)
                    <option value="{{ $instructeur['id'] }}">{{ $instructeur['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Salle</label>
            <select wire:model.live="salleFilter">
                <option value="">Toutes les salles</option>
                @foreach ($filters['salles'] as $salle)
                    <option value="{{ $salle['id'] }}">{{ $salle['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="planning-filter-field">
            <label>Statut</label>
            <select wire:model.live="statutFilter">
                <option value="">Tous les statuts</option>
                <option value="brouillon">Brouillon</option>
                <option value="planifiee">Planifiée</option>
                <option value="confirmee">Confirmée</option>
                <option value="annulee">Annulée</option>
                <option value="terminee">Terminée</option>
            </select>
        </div>

        <div class="planning-filter-reset">
            <button type="button" class="planning-button" wire:click="resetFilters">
                Réinitialiser
            </button>
        </div>
    </div>

    @livewire(\Modules\FPSplanificationstage\Filament\Widgets\PlanningCalendar::class, [
        'stageFilter' => $this->stageFilter,
        'instructeurFilter' => $this->instructeurFilter,
        'salleFilter' => $this->salleFilter,
        'statutFilter' => $this->statutFilter,
    ], key($this->filterKey()))
</x-filament-panels::page>
