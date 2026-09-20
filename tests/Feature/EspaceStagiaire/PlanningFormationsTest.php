<?php

namespace Modules\FPSplanificationstage\Tests\Feature\EspaceStagiaire;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Modules\FPSplanificationstage\Filament\Pages\EspaceStagiaire\PlanningFormations;
use Tests\TestCase;

class PlanningFormationsTest extends TestCase
{
    public function test_page_metadata_matches_espace_stagiaire(): void
    {
        $this->assertSame(
            'Planning des formations',
            PlanningFormations::getNavigationLabel()
        );

        $this->assertSame(
            'Espace stagiaire',
            PlanningFormations::getNavigationGroup()
        );
    }

    public function test_page_is_registered_in_fpsplanificationstage_panel(): void
    {
        $panel =
            Filament::getPanel(
                'fpsplanificationstage'
            );

        $this->assertContains(
            PlanningFormations::class,
            $panel->getPages()
        );
    }

    public function test_page_view_exists_and_is_a_filament_page(): void
    {
        $path =
            base_path(
                'Modules/FPSplanificationstage/'
                . 'resources/views/filament/pages/'
                . 'espace-stagiaire/'
                . 'planning-formations.blade.php'
            );

        $this->assertFileExists(
            $path
        );

        $source =
            file_get_contents(
                $path
            );

        $this->assertIsString(
            $source
        );

        $this->assertStringContainsString(
            'ESPACE_STAGIAIRE_PLANNING_FILAMENT_V1',
            $source
        );

        $this->assertStringContainsString(
            '<x-filament-panels::page>',
            $source
        );

        $this->assertStringContainsString(
            'PORTAIL_VUE_SEMAINE_V1',
            $source
        );

        $this->assertStringContainsString(
            'ps-stagiaire-planning',
            $source
        );
    }

    public function test_planning_page_reloads_calendar_when_filters_change(): void
    {
        $path =
            base_path(
                'Modules/FPSplanificationstage/'
                . 'resources/views/filament/pages/'
                . 'planning.blade.php'
            );

        $source = file_get_contents($path);

        $this->assertIsString($source);
        $this->assertStringContainsString(
            'wire:model.live="stageFilter"',
            $source
        );
        $this->assertStringContainsString(
            'key($this->filterKey())',
            $source
        );
    }

    public function test_page_class_reuses_existing_planning_logic(): void
    {
        $path =
            base_path(
                'Modules/FPSplanificationstage/'
                . 'app/Filament/Pages/'
                . 'EspaceStagiaire/'
                . 'PlanningFormations.php'
            );

        $source =
            file_get_contents(
                $path
            );

        $this->assertIsString(
            $source
        );

        $this->assertStringContainsString(
            'PublicPlanningController::class',
            $source
        );

        $this->assertStringContainsString(
            '->getData()',
            $source
        );
    }

    public function test_generated_blade_source_compiles(): void
    {
        $path =
            base_path(
                'Modules/FPSplanificationstage/'
                . 'resources/views/filament/pages/'
                . 'espace-stagiaire/'
                . 'planning-formations.blade.php'
            );

        $source =
            file_get_contents(
                $path
            );

        $this->assertIsString(
            $source
        );

        $compiled =
            Blade::compileString(
                $source
            );

        $this->assertNotSame(
            '',
            trim(
                $compiled
            )
        );
    }
}
