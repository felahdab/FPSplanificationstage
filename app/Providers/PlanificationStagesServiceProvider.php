<?php

namespace Modules\PlanificationStages\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class PlanificationStagesServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'PlanificationStages';

    protected string $nameLower = 'planificationstages';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(
            module_path(
                $this->name,
                'database/migrations'
            )
        );
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(
            EventServiceProvider::class
        );

        $this->app->register(
            RouteServiceProvider::class
        );

        $this->registerPolicies();
    }

    public function registerPolicies(): void
    {
        $policies = [
            // Model::class => ModelPolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy(
                $model,
                $policy
            );
        }
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // Exemple :
        //
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(
        //         \Illuminate\Console\Scheduling\Schedule::class
        //     );
        //
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path(
            'lang/modules/' . $this->nameLower
        );

        $moduleLangPath = module_path(
            $this->name,
            'resources/lang'
        );

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom(
                $langPath,
                $this->nameLower
            );

            $this->loadJsonTranslationsFrom(
                $langPath
            );

            return;
        }

        if (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom(
                $moduleLangPath,
                $this->nameLower
            );

            $this->loadJsonTranslationsFrom(
                $moduleLangPath
            );
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path(
            $this->name,
            'config/config.php'
        );

        if (! is_file($configPath)) {
            return;
        }

        $this->publishes(
            [
                $configPath =>
                    config_path(
                        $this->nameLower . '.php'
                    ),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            $configPath,
            $this->nameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path(
            'views/modules/' . $this->nameLower
        );

        $sourcePath = module_path(
            $this->name,
            'resources/views'
        );

        $this->publishes(
            [
                $sourcePath => $viewPath,
            ],
            [
                'views',
                $this->nameLower . '-module-views',
            ]
        );

        $this->loadViewsFrom(
            array_merge(
                $this->getPublishableViewPaths(),
                [$sourcePath]
            ),
            $this->nameLower
        );

        $componentNamespace =
            $this->module_namespace(
                $this->name,
                $this->app_path(
                    config(
                        'modules.paths.generator.component-class.path'
                    )
                )
            );

        Blade::componentNamespace(
            $componentNamespace,
            $this->nameLower
        );
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (
            config('view.paths')
            as $path
        ) {
            $moduleViewPath =
                $path
                . '/modules/'
                . $this->nameLower;

            if (is_dir($moduleViewPath)) {
                $paths[] =
                    $moduleViewPath;
            }
        }

        return $paths;
    }
}