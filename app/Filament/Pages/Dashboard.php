<?php

namespace Modules\PlanificationStages\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Modules\PlanificationStages\Filament\Widgets\TableauBordSemaine;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel =
        'Tableau de bord';

    protected static ?int $navigationSort =
        -100;

    public function getTitle(): string
    {
        return 'Tableau de bord';
    }

    public function getColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            TableauBordSemaine::class,
        ];
    }
}