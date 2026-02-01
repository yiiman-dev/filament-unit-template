<?php

namespace Modules\Basic\BaseKit;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\Facades\File;

abstract class BaseFilamentProvider extends PanelProvider
{
    protected function modules(Panel $panel)
    {
        $panel_name = strtolower($panel->getId());
        $cacheFilePath = storage_path('framework/cache/filament_modules.json');
        if (!(File::exists($cacheFilePath)) && !$this->app->runningInConsole()) {
            throw new \ErrorException("Please first run this command to serve filament modules:
            php artisan filament:list-modules");
            // Optionally capture output
        }

        if (File::exists($cacheFilePath) && !empty($modules = File::json($cacheFilePath))) {
            if (empty($modules[$panel_name])) return $panel;
            foreach ($modules[$panel_name] as $path => $namespace) {
                $panel
                    ->discoverResources(in: base_path($path), for: str_replace('/', '//', $path));
//                    ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages');
            }
        }

        return $panel;
    }
}
