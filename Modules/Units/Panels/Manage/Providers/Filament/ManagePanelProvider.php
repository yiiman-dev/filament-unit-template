<?php

namespace Units\Panels\Manage\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Basic\BaseKit\BaseColor;
use Modules\Basic\BaseKit\BaseFilamentProvider;
use Units\ManagePlugins;
use Units\Sessions\Manage\Middlewares\ManageSessionMiddleware;

class ManagePanelProvider extends BaseFilamentProvider
{
    /**
     * @throws \ErrorException
     */
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('manage')
            ->path('manage')
            ->authGuard('manage')
            ->colors([
                'gray' => BaseColor::Gray,
                'primary' => BaseColor::Success,
                'success' => BaseColor::Success,
                'danger' => BaseColor::Danger,
                'warning' => BaseColor::Warning,
                'info' => BaseColor::Info,
            ])
            ->plugins([
                ManagePlugins::make(),
            ])
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages') // Dashboard از طریق پلاگین ثبت می‌شود
            ->middleware([
                EncryptCookies::class,
                ManageSessionMiddleware::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                \Units\Sessions\Shared\Middlewares\SharedVerifyCsrfTokenMiddleware::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseTransactions(false)//Do not enable that
            ->brandName('کارگزار')
            ->brandLogo(asset('image/logo.png'))
            ->brandLogoHeight('200')
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
