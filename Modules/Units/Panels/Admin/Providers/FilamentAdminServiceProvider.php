<?php

namespace Units\Panels\Admin\Providers;

use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Modules\Basic\Concerns\KnownUnitDB;
use Modules\Basic\Concerns\RetrieveFilamentProviderTrait;
use Nwidart\Modules\Traits\PathNamespace;
use Units\ActLog\Admin\Providers\AdminActLogProvider;

class FilamentAdminServiceProvider extends ServiceProvider
{
    use KnownUnitDB;
    use PathNamespace;
    use RetrieveFilamentProviderTrait;

    protected string $name = 'FilamentAdmin';

    protected string $nameLower = 'filamentadmin';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerFilamentPanel('Admin');
        //        Filament::registerScript('filament-navigation', asset('js/filament-navigation.js'));
        $this->loadUnitDatabaseResources();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(PackageServiceProvider::class);
        $this->app->register(AdminActLogProvider::class);
        $this->app->singleton(SessionGuard::class);

        Auth::provider('user_admin_provider', function ($app, array $config) {
            return new EluquentAdminProvider(new BcryptHasher, $config['model']);
        });
    }
}
