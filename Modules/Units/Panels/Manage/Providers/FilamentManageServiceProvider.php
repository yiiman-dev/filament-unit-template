<?php

namespace Units\Panels\Manage\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\SessionGuard;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Modules\Basic\Concerns\KnownUnitDB;
use Modules\Basic\Concerns\RetrieveFilamentProviderTrait;
use Nwidart\Modules\Traits\PathNamespace;
use Units\Auth\Manage\Services\AuthService;

class FilamentManageServiceProvider extends ServiceProvider
{
    use KnownUnitDB;
    use PathNamespace;
    use RetrieveFilamentProviderTrait;

    protected string $name = 'FilamentManage';

    protected string $nameLower = 'filamentmanage';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerFilamentPanel('Manage');
        FilamentAsset::register([
            Js::make(
                'filament-navigation',
                base_path('Modules/Units/Panels/Common/Assets/js/filament-navigation.js')
            ),
        ]);
        $this->loadUnitDatabaseResources();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(PackageServiceProvider::class);
        $this->app->singleton(AuthService::class);
        $this->app->singleton(SessionGuard::class);
        Auth::provider('user_manage_provider', function ($app, array $config) {
            return new EluquentManageProvider(new BcryptHasher, $config['model']);
        });
    }
}
