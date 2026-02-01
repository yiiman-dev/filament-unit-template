<?php

namespace Units\Settings\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;

class AdminSettingsPlugin implements Plugin
{

    public function getId(): string
    {
        return 'admin_settings';
    }

    public static function make(): self{
        return new static();
    }

    public function register(Panel $panel): void
    {
        // TODO: Implement register() method.
    }

    public function boot(Panel $panel): void
    {
        $panel->plugins(
            [
                FilamentSettingsPlugin::make()
                ->pages([

                ])
            ]
        );
    }
}
