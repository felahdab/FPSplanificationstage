<x-filament-panels::page>
    @php
        $stats = $this->statsData();
        $years = $this->availableYears();
    @endphp

    <x-filament::section>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="text-xl font-bold">
                Statistiques de l'année {{ $stats['year'] }}
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-filament::button
                    type="button"
                    color="gray"
                    icon="heroicon-o-chevron-left"
                    wire:click="previousYear"
                >
                    Année précédente
                </x-filament::button>

                <select
                    class="fi-input block w-28"
                    wire:model.live="selectedYear"
                >
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <x-filament::button
                    type="button"
                    wire:click="currentYear"
                >
                    Année actuelle
                </x-filament::button>

                <x-filament::button
                    type="button"
                    color="gray"
                    icon="heroicon-o-chevron-right"
                    icon-position="after"
                    wire:click="nextYear"
                >
                    Année suivante
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    @foreach ($this->statSections() as $section)
        <x-filament::section :heading="$section['title']">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($section['items'] as $item)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm {{ $item['tone'] }}">
                        <div class="text-3xl font-bold leading-none">
                            {{ $item['value'] }}
                        </div>

                        <div class="mt-2 text-sm text-gray-600">
                            {{ $item['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endforeach

    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
        Les statistiques sont calculées pour l'année sélectionnée. Une session est considérée comme réalisée lorsque sa date de fin est passée et qu'elle n'est pas annulée.
    </div>
</x-filament-panels::page>
