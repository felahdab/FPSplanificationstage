<?php

namespace Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\FPSplanificationstage\Filament\Widgets\PlanningCalendar;

class PlanningFormations extends Page
{
    protected static ?string $navigationLabel =
        'Planning des formations';

    protected static string|\UnitEnum|null $navigationGroup =
        'Espace stagiaire';

    protected static ?int $navigationSort =
        10000;

    protected static ?string $slug =
        'espace-stagiaire/planning-formations';

    public string $searchTerm = '';

    public function getTitle(): string
    {
        return 'Planning des formations';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Recherche')
                ->schema([
                    Grid::make(['lg' => 1])
                        ->schema([
                            TextInput::make('searchTerm')
                                ->label('Rechercher')
                                ->placeholder('Nom de formation, lieu, service...')
                                ->live(onBlur: false)
                                ->debounce(300),
                        ]),
                ]),
            Section::make('Calendrier')
                ->schema([
                    Livewire::make(PlanningCalendar::class, [
                        'stageFilter' => '',
                        'instructeurFilter' => '',
                        'salleFilter' => '',
                        'statutFilter' => '',
                        'searchTerm' => $this->searchTerm,
                    ])
                        ->key('planning-formations-calendar-' . md5($this->searchTerm)),
                ]),
        ]);
    }
}
