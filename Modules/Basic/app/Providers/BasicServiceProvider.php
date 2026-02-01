<?php

namespace Modules\Basic\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Kavenegar\KavenegarApi;
use Mckenziearts\Notify\LaravelNotifyServiceProvider;
use Modules\Basic\Console\Commands\MakeDTOFromMigration;
use Modules\Basic\Models\APIConnection;
use Nwidart\Modules\Traits\PathNamespace;
use RealRashid\SweetAlert\SweetAlertServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Units\SMS\Common\Services\BaseSmsService;

class BasicServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Basic';

    protected string $nameLower = 'basic';

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
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));


    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Add api model database connection driver
        $this->app->resolving('db', function ($db) {
            $db->extend('api_driver', function ($config, $name) {
                $config['name'] = $name;
                return new APIConnection($config);
            });
        });
//        DB::extend('api_driver', function ($config, $name) {
//            // Return an instance of your custom connection class here
//            return new APIConnection(/* ... */);
//        });
        $this->app->register(EventServiceProvider::class);
//        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(BaseSmsService::class, function ($app) {
            return new BaseSmsService();
        });
        $this->app->singleton(KavenegarApi::class, function ($app) {
            return new KavenegarApi(env('KAVENEGAR_API_KEY'));
        });

        if (class_exists('Mckenziearts\Notify\LaravelNotifyServiceProvider')) {
            $this->app->register(LaravelNotifyServiceProvider::class);
        }

        // Register Faker service provider
        $this->app->register(FakerServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Basic\Console\Commands\SetupCommand::class,
            \Modules\Basic\Console\Commands\RunCommand::class,
            \Modules\Basic\Console\Commands\CheckDatabaseHealthCommand::class,
            MakeDTOFromMigration::class,
            \App\Console\Commands\SyncCeoPermissions::class
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            // Sync CEO permissions daily at 2 AM
            $schedule->command('ceo:sync-permissions')->dailyAt('02:0');
        });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->name);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
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
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
