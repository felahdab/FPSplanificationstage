<?php

namespace Modules\FPSplanificationstage\Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;

class PublicCalendrierPageTest extends TestCase
{
    private function data(
        string $viewMode
    ): array {
        return [
            'days' => [],
            'moisLabel' => 'Septembre 2026',
            'moisPrecedent' => '2026-08',
            'moisSuivant' => '2026-10',
            'moisActuel' => '2026-09',
            'moisCourant' => '2026-09',

            'weekDays' => [],
            'weekLabel' =>
                'Semaine du 14 au 18 septembre 2026',
            'semainePrecedente' => '2026-09-07',
            'semaineSuivante' => '2026-09-21',
            'semaineActuelle' => '2026-09-14',
            'semaineCourante' => '2026-09-14',

            'viewMode' => $viewMode,
            'searchTerm' => '',
            'listSessions' => new Collection(),
            'matchingSessionsCount' => null,
        ];
    }

    public function test_calendar_view_renders_without_database(): void
    {
        $this
            ->view(
                'fpsplanificationstage::public.calendrier',
                $this->data('calendrier')
            )
            ->assertSee(
                'Portail des formations'
            );
    }

    public function test_week_view_renders_without_database(): void
    {
        $this
            ->view(
                'fpsplanificationstage::public.calendrier',
                $this->data('semaine')
            )
            ->assertSee(
                'Semaine du 14 au 18 septembre 2026'
            );
    }

    public function test_list_view_renders_without_database(): void
    {
        $this
            ->view(
                'fpsplanificationstage::public.calendrier',
                $this->data('liste')
            )
            ->assertSee(
                'Portail des formations'
            );
    }

    public function test_skeletor_style_is_present(): void
    {
        $path =
            base_path(
                'Modules/FPSplanificationstage/'
                . 'resources/views/public/calendrier.blade.php'
            );

        $source =
            file_get_contents($path);

        $this->assertIsString($source);

        $this->assertStringContainsString(
            'PORTAIL_SKELETOR_STYLE_V1_3',
            $source
        );

        $this->assertStringContainsString(
            'PORTAIL_VUE_SEMAINE_V1',
            $source
        );

        $this->assertStringContainsString(
            'PORTAIL_URLS_RELATIVES_V1',
            $source
        );
    }
}
