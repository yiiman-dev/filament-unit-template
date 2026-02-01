<?php

namespace Modules\Basic\Concerns;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait RetrieveFilamentProviderTrait
{

    /**
     * Register all resources for a Filament panel
     *
     * @param string $panelDirectoryName The panel directory name (e.g., 'Admin', 'My', 'Manage')
     * @return void
     */
    public function registerFilamentPanel(string $panelDirectoryName): void
    {
        $basePath = app()->basePath('Modules/Units');
        $panelDirectoryName = ucfirst($panelDirectoryName);

        // Find all panel directories
        $panelUnits = $this->findPanelUnits($basePath, $panelDirectoryName);

        foreach ($panelUnits as $panelPath) {
            // Get the unit name from the parent directory of the panel
            $filament_id=str(request()->path())->before('/')->toString();
            $unitName = basename(dirname($panelPath));
            $namespace = strtolower($panelDirectoryName) . '_' . strtolower($unitName);
            $common_namespace='common_'.strtolower($unitName);
            $common_path=str($panelPath)->beforeLast($panelDirectoryName).'Common';
            // Register views
            $this->registerPanelViews($panelPath, $namespace, $unitName);
            $this->registerPanelViews($common_path,$common_namespace , $unitName);

            // Register components
            $this->registerPanelComponents($panelPath, $namespace, $unitName);
            $this->registerPanelComponents($common_path, $common_namespace, $unitName);

            // Register Livewire components
            $this->registerPanelLivewireComponents($panelPath, $unitName);
            $this->registerPanelLivewireComponents($common_path, $unitName);

            // Register translations
            $this->registerPanelTranslations($panelPath, $namespace);

            // Register configs
            if (str($namespace)->contains( $filament_id)){
                $this->registerPanelConfigs($panelPath, $namespace);
            }
            $this->registerPanelConfigs($common_path, $common_namespace);
            // Register commands
            $this->registerPanelCommands($panelPath);
            $this->registerPanelCommands($common_path);

            // Register public assets
            $this->publish_assets($panelPath, $unitName, $panelDirectoryName);
        }
    }

    /**
     * Find all units that belong to a specific panel
     */
    private function findPanelUnits(string $basePath, string $panelDirectoryName): array
    {
        $units = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                $panelPath = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . $panelDirectoryName;
                if (is_dir($panelPath)) {
                    $units[] = $panelPath;
                }
            }
        }

        return $units;
    }


    private function publish_assets(string $panelPath, string $unitName, string $panelName): void
    {
        $publicPath = $panelPath . DIRECTORY_SEPARATOR . 'Public';

        if (!is_dir($publicPath)) {
            $publicPath = $panelPath . DIRECTORY_SEPARATOR . 'public';
            if (!is_dir($publicPath)) {
                return;
            }
        }


        $targetPath = public_path('assets'.DIRECTORY_SEPARATOR.strtolower($panelName) .DIRECTORY_SEPARATOR. strtolower($unitName)  );


        // Register the public directory for publishing

            $this->publishes([
                $publicPath => $targetPath,
            ], 'public-'.strtolower($panelName));

    }

    /**
     * Register views for a panel unit
     */
    private function registerPanelViews(string $panelPath, string $namespace, string $unitName): void
    {
        $viewPath = $panelPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
        if (!is_dir($viewPath)) {
            return;
        }

        $publishPath = resource_path('views/modules/' . $namespace);

        // Publish views
        if ($this->app->runningInConsole()) {
            $this->publishes([$viewPath => $publishPath], ['views', $namespace . '-module-views']);
        }

        // Load views
        $this->loadViewsFrom($viewPath, $namespace);
    }

    /**
     * Register Blade components for a panel unit
     */
    private function registerPanelComponents(string $panelPath, string $namespace, string $unitName): void
    {
        $componentPath = $panelPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'components';
        if (!is_dir($componentPath)) {
            return;
        }

        // Register component namespace using the unit name
        $componentNamespace = $this->module_namespace(
            $unitName,
            $this->app_path(config('modules.paths.generator.component-class.path'))
        );

        Blade::componentNamespace($componentNamespace, $namespace);
    }

    /**
     * Register translations for a panel unit
     */
    private function registerPanelTranslations(string $panelPath, string $namespace): void
    {
        $langPath = $panelPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
        if (!is_dir($langPath)) {
            return;
        }

        $publishPath = resource_path('lang/modules/' . $namespace);

        // Publish translations
        if ($this->app->runningInConsole()) {
            $this->publishes([$langPath => $publishPath], ['lang', $namespace . '-module-lang']);
        }

        // Load translations
        $this->loadTranslationsFrom($langPath, $namespace);
        $this->loadJsonTranslationsFrom($langPath);
    }

    /**
     * Register configs for a panel unit
     */
    private function registerPanelConfigs(string $panelPath, string $namespace): void
    {
        $configPath = $panelPath . DIRECTORY_SEPARATOR . 'config';
        if (!is_dir($configPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($configPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                if ($relativePath=='filament-shield.php'||$relativePath=='permission.php'){
                    $configKey = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                }else{
                    $configKey = $namespace . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                }
                $key = ($relativePath === 'config.php') ? $namespace : $configKey;

                // Publish config
                if ($this->app->runningInConsole()) {
                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                }
                if ($key=='filament-shield'||$key=='permission'){
                    app()->make('config')->set($key,require $file->getPathname());
                }else{
                    // Merge config
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register commands for a panel unit
     */
    private function registerPanelCommands(string $panelPath): void
    {
        $commandPath = $panelPath . DIRECTORY_SEPARATOR . 'Console' . DIRECTORY_SEPARATOR . 'Commands';
        if (!is_dir($commandPath)) {
            return;
        }

        $files = File::allFiles($commandPath);
        $commands = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className && is_subclass_of($className, \Illuminate\Console\Command::class)) {
                    $commands[] = $className;
                }
            }
        }

        if (!empty($commands)) {
            $this->commands($commands);
        }
    }

    /**
     * Get class name from a PHP file
     */
    private function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/i', $content, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/i', $content, $matches)) {
                return $namespace . '\\' . $matches[1];
            }
        }
        return null;
    }

    /**
     * Register Livewire components for a panel unit
     */
    private function registerPanelLivewireComponents(string $panelPath, string $unitName): void
    {
        $livewirePath = $panelPath . DIRECTORY_SEPARATOR . 'Livewire';
        if (!is_dir($livewirePath)) {
            return;
        }

        $files = File::allFiles($livewirePath);
        $livewireNamespace = $this->module_namespace(
            $unitName,
            'Livewire'
        );

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className && is_subclass_of($className, \Livewire\Component::class)) {
                    $componentName = class_basename($className);
                    $componentName = strtolower(str_replace('Component', '', $componentName));
                    \Livewire\Livewire::component($componentName, $className);
                }
            }
        }
    }
}
