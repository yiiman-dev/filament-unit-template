<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/5/25, 3:11 PM
 */

namespace Units\Panels\Manage\Providers;

use Illuminate\Support\Facades\Blade;
use Modules\FilamentAdmin\Filament\Pages\Auth\VerifyComponent;
use Spatie\LaravelPackageTools\Package;
use Units\Auth\Admin\Filament\Pages\AuthLayoutComponent;
use Units\Panels\Admin\Filament\Pages\BaseLayoutComponent;

class PackageServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-manage')
            ->hasViews();
    }

    public function packageBooted()
    {

//        Livewire::
//        Blade::componentNamespace('Modules\\FilamentAdmin\\Filament\\Views\\Pages', 'page');
////        Blade::componentNamespace('Modules\\FilamentAdmin\\Filament\\Views\\Components', 'components');
        Blade::components([
            BaseLayoutComponent::class,
            AuthLayoutComponent::class
        ]);


    }
}
