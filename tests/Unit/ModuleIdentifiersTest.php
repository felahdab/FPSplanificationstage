<?php

use RecursiveDirectoryIterator as DirectoryIterator;

uses()->group('FPSplanificationstage');

function moduleRoot(): string
{
    return dirname(__DIR__, 2);
}

it('module json uses new module identity', function () {
    $data = json_decode(
        file_get_contents(moduleRoot() . '/module.json'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($data['name'])->toBe('FPSplanificationstage')
        ->and($data['alias'])->toBe('fpsplanificationstage')
        ->and($data['providers'])->toContain('Modules\\FPSplanificationstage\\Providers\\FPSplanificationstageServiceProvider')
        ->toContain('Modules\\FPSplanificationstage\\Providers\\Filament\\FilamentPanelProvider');
});

it('module composer uses new psr4 namespace', function () {
    $data = json_decode(
        file_get_contents(moduleRoot() . '/composer.json'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($data['name'])->toBe('nwidart/fpsplanificationstage')
        ->and($data['autoload']['psr-4'])->toHaveKey('Modules\\FPSplanificationstage\\')
        ->not->toHaveKey('Modules\\PlanificationStages\\');
});

it('active source does not reference old php namespace', function () {
    $root = moduleRoot();
    $directories = ['app', 'config', 'routes', 'resources', 'database/seeders'];
    $violations = [];

    foreach ($directories as $directory) {
        $path = $root . '/' . $directory;

        if (! is_dir($path)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            $allowed = ['php', 'blade.php', 'json', 'js'];

            if (! in_array($extension, $allowed, true) && ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            if (str_contains((string) $content, 'Modules\\PlanificationStages')) {
                $violations[] = str_replace($root . '/', '', $file->getPathname());
            }
        }
    }

    expect($violations)->toBe([]);
});
