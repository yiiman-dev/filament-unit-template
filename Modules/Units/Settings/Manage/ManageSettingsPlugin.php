<?php

namespace Units\Settings\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use Units\Settings\Manage\Filament\Pages\BasicParameterSettings;


class ManageSettingsPlugin implements Plugin
{

    public function getId(): string
    {
        return 'manage_settings';
    }

    public static function make(): self{
        return new static();
    }

    public function register(Panel $panel): void
    {
        $panel->plugins(
            [
                FilamentSettingsPlugin::make()
                    ->pages([
                        BasicParameterSettings::class,
//                        FormsAndContentSettings::class,
//                        FinanceRequestSettings::class,
//                        FinancialSettings::class,
//                        GlobalSettings::class,
//                        SmsSettings::class
                    ])
            ]
        );
    }

    public function boot(Panel $panel): void
    {

    }
}
