<?php

namespace App\Providers;

use App\Console\Commands\UnitMigrationMake;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        URL::macro(
            'alternateHasCorrectSignature',
            function (Request $request, $absolute = true, array $ignoreQuery = []) {
                $ignoreQuery[] = 'signature';

                $absoluteUrl = url($request->path());
                $url = $absolute ? $absoluteUrl : '/' . $request->path();

                $queryString = collect(
                    explode(
                        '&',
                        (string)$request
                            ->server->get('QUERY_STRING')
                    )
                )
                    ->reject(fn($parameter) => in_array(Str::before($parameter, '='), $ignoreQuery))
                    ->join('&');

                $original = rtrim($url . '?' . $queryString, '?');

                // Use the application key as the HMAC key
                $key = config('app.key'); // Ensure app.key is properly set in .env

                if (empty($key)) {
                    throw new \RuntimeException('Application key is not set.');
                }

                $signature = hash_hmac('sha256', $original, $key);
                return hash_equals($signature, (string)$request->query('signature', ''));
            }
        );

        URL::macro(
            'alternateHasValidSignature',
            function (Request $request, $absolute = true, array $ignoreQuery = []) {
                return URL::alternateHasCorrectSignature($request, $absolute, $ignoreQuery)
                    && URL::signatureHasNotExpired($request);
            }
        );

        Request::macro('hasValidSignature', function ($absolute = true, array $ignoreQuery = []) {
            return URL::alternateHasValidSignature($this, $absolute, $ignoreQuery);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        if (env('USE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        $this->app->singleton(UnitMigrationMake::class, function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new UnitMigrationMake($creator, $composer);
        });
    }
}
