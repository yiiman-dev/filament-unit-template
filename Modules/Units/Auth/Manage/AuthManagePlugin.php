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

namespace Units\Auth\Manage;

use Filament\Contracts\Plugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Units\Auth\Manage\Filament\Components\LoginComponent;
use Units\Auth\Manage\Filament\Pages\ProfilePage;
use Units\Auth\Manage\Filament\Pages\VerifyPage;

class AuthManagePlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-auth-manage-plugin';
    }

    public static function make()
    {
        return new static();
    }

    public function register(Panel $panel): void
    {
        $panel
            ->login(
                LoginComponent::class
            )
            ->resources([

            ])
            ->pages([
                ProfilePage::class,
                VerifyPage::class,
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
            // ->widgets([
            //     AccountWidget::class, // حذف شد - ویجت خوش آمدید
            // ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
