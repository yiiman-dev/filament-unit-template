<?php

namespace Modules\Basic\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Basic\Helpers\NationalCodeFakerProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Check if Faker is available and register our provider
        if (class_exists(\Faker\Factory::class)) {
            $this->app->singleton('faker', function ($app) {
                $faker = \Faker\Factory::create(config('faker.locale', 'fa_IR'));

                // Add our custom provider
                $faker->addProvider(new NationalCodeFakerProvider($faker));

                return $faker;
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
