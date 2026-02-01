<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Users\Manage\My;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Units\Users\Manage\My\Filament\Resources\UserResource;

class ManageMyUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Filament Resources
        $this->registerFilamentResources();
    }
    
    /**
     * Register Filament Resources
     */
    protected function registerFilamentResources(): void
    {
        $this->app->resolving(Panel::class, function (Panel $panel) {
            if ($panel->getId() === 'admin') {
                $panel->getResources([
                    UserResource::class,
                ]);
            }
        });
    }
} 