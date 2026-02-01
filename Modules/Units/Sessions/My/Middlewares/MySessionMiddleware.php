<?php

namespace Units\Sessions\My\Middlewares;

use Closure;
use Illuminate\Support\Facades\Config;

class MySessionMiddleware
{
    public function handle($request, Closure $next)
    {
        // Ensure proper session configuration for the 'my' panel
        Config::set('session.connection', 'my');
        Config::set('session.cookie', 'known_as_my');
        return $next($request);
    }
}
