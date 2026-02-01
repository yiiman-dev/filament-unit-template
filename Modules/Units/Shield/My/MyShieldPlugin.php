<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/3/25, 5:58 PM
 */

namespace Units\Shield\My;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Spatie\Permission\PermissionRegistrar;
use Units\Shield\Common\ShieldHelper;

class MyShieldPlugin implements Plugin
{
    public static function make()
    {
        return new static;
    }

    public function getId(): string
    {
        return 'my-shield-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->resources([
                \Units\Shield\My\Filament\RoleResource::class,
            ])
            ->bootUsing(function (Panel $panel) {
                ShieldHelper::setConfig($panel->getId());
            });
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
