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

namespace Units\Auth\My;

use Filament\Contracts\Plugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\UserMenuItem;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Modules\Basic\Helpers\Helper;
use Units\Auth\Common\Middlewares\CheckLogOutPalseMiddleware;
use Units\Auth\Common\Middlewares\OnlinePalseMiddleware;
use Units\Auth\My\Filament\Components\LoginComponent;
use Units\Auth\My\Filament\Pages\Auth\Register\LegalPage;
use Units\Auth\My\Filament\Pages\Auth\Register\NaturalPage;
use Units\Auth\My\Filament\Pages\Auth\Register\SelectPersonTypePage;
use Units\Auth\My\Filament\Pages\Auth\Register\Show;
use Units\Auth\My\Filament\Pages\Auth\VerifyPage;
use Units\Auth\My\Filament\Pages\ProfilePage;
use Units\Corporates\Placed\Common\Models\CorporateModel;


class AuthMyPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-auth-my-plugin';
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
                Show::class,
                ProfilePage::class,
                VerifyPage::class,
                SelectPersonTypePage::class,
                NaturalPage::class,
                LegalPage::class,
            ])
            ->tenantMiddleware([
                CheckLogOutPalseMiddleware::class,
                OnlinePalseMiddleware::class
            ])
//            ->userMenuItems([
//                UserMenuItem::make()
//                ->url(ProfilePage::getNavigationUrl())
//                    ->label('پروفایل کاربری')
//                ->icon('heroicon-o-user-circle')
//            ])
            ->widgets([
                AccountWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
