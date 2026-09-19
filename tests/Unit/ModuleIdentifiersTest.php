<?php

namespace Modules\FPSplanificationstage\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleIdentifiersTest extends TestCase
{
    private function moduleRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_module_json_uses_new_module_identity(): void
    {
        $data = json_decode(
            file_get_contents(
                $this->moduleRoot() . '/module.json'
            ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame(
            'FPSplanificationstage',
            $data['name']
        );

        self::assertSame(
            'fpsplanificationstage',
            $data['alias']
        );

        self::assertContains(
            'Modules\\FPSplanificationstage\\Providers\\FPSplanificationstageServiceProvider',
            $data['providers']
        );

        self::assertContains(
            'Modules\\FPSplanificationstage\\Providers\\Filament\\FilamentPanelProvider',
            $data['providers']
        );
    }

    public function test_module_composer_uses_new_psr4_namespace(): void
    {
        $data = json_decode(
            file_get_contents(
                $this->moduleRoot() . '/composer.json'
            ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame(
            'nwidart/fpsplanificationstage',
            $data['name']
        );

        self::assertArrayHasKey(
            'Modules\\FPSplanificationstage\\',
            $data['autoload']['psr-4']
        );

        self::assertArrayNotHasKey(
            'Modules\\PlanificationStages\\',
            $data['autoload']['psr-4']
        );
    }

    public function test_active_source_does_not_reference_old_php_namespace(): void
    {
        $root = $this->moduleRoot();

        $directories = [
            'app',
            'config',
            'routes',
            'resources',
            'database/seeders',
        ];

        $violations = [];

        foreach ($directories as $directory) {
            $path = $root . '/' . $directory;

            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $path,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $extension = strtolower(
                    $file->getExtension()
                );

                if (! in_array(
                    $extension,
                    [
                        'php',
                        'blade.php',
                        'json',
                        'js',
                    ],
                    true
                )) {
                    $name = $file->getFilename();

                    if (! str_ends_with(
                        $name,
                        '.blade.php'
                    )) {
                        continue;
                    }
                }

                $content = file_get_contents(
                    $file->getPathname()
                );

                if (
                    str_contains(
                        $content,
                        'Modules\\PlanificationStages'
                    )
                ) {
                    $violations[] =
                        str_replace(
                            $root . '/',
                            '',
                            $file->getPathname()
                        );
                }
            }
        }

        self::assertSame(
            [],
            $violations,
            'Ancien namespace actif détecté : '
                . implode(', ', $violations)
        );
    }
}
