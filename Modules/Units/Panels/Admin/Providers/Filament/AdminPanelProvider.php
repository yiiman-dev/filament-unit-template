<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:46 PM
 */

namespace Units\Panels\Admin\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Basic\BaseKit\BaseColor;
use Modules\Basic\BaseKit\BaseFilamentProvider;
use Units\AdminPlugins;
use Units\Sessions\Admin\Middlewares\AdminSessionMiddleware;
use Units\Users\Admin\Manage\Filament\Resources\UserResource;

class AdminPanelProvider extends BaseFilamentProvider
{
    /**
     * @throws \ErrorException
     */
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->plugins([
                AdminPlugins::make(),
            ])
            ->colors([
                'gray' => BaseColor::Gray,
                'primary' => BaseColor::Primary,
                'success' => BaseColor::Success,
                'danger' => BaseColor::Danger,
                'warning' => BaseColor::Warning,
                'info' => BaseColor::Info,
            ])

            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                UserResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AdminSessionMiddleware::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                \Units\Sessions\Shared\Middlewares\SharedVerifyCsrfTokenMiddleware::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->brandLogo(asset('image/logo.png'))
            ->brandName('مدیریت')
            ->brandLogoHeight('3.5rem')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->font('Shabnam', asset('css/fonts.css'))
            ->favicon(asset('image/logo.png'))
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->sidebarWidth('19rem');

        $this->modules($panel);

        return $panel;
    }
}
