<?php

namespace Units\Auth\Common\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::middleware('web')->group(app()->basePath( 'Modules/Units/Auth/My/routes/web.php'));
    }
}
