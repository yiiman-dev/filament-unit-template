<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:16 AM
 */

namespace Units\Auth\Admin;

use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Units\Auth\Admin\Filament\Components\LoginComponent;
use Units\Auth\Admin\Filament\Pages\ProfilePage;
use Units\Auth\Admin\Filament\Pages\VerifyPage;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource;

class AuthAdminPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-auth-admin-plugin';
    }

    public static function make()
    {
        return new static();
    }

    public function register(Panel $panel): void
    {
        $panel
            ->login(LoginComponent::class)
            ->resources([
                AdminUsersResource::class
            ])
            ->pages([
                ProfilePage::class,
                VerifyPage::class
            ])
            ->userMenuItems([
                // Add a "Profile" link at the top of the user menu
                'profile' => MenuItem::make()
                    ->label('پروفایل من')
                    ->icon('heroicon-o-user')
                    ->url(fn() => ProfilePage::getUrl()),

                // You can add more items if needed
                // MenuItem::make()->label('تنظیمات')->icon('heroicon-o-cog')->url(...),
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
