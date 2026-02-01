<?php

namespace Units\Auth\Admin\Providers;

use Closure;
use Filament\Facades\Filament;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Units\Users\Admin\Manage\Filament\Resources\UserResource;

class AdminUnitProvider extends PackageServiceProvider
{
    public function boot():void
    {
        Filament::getCurrentPanel()->resources(
            [
                UserResource::class,
            ]
        );
    }

    public function configurePackage(Package $package): void
    {
        $package->name('filament-admin-auth');
    }
    public function booted(Closure $callback)
    {

    }
}
