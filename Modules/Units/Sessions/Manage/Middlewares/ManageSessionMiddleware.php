<?php

namespace Units\Sessions\Manage\Middlewares;

use Closure;

class ManageSessionMiddleware
{
    public function handle($request, Closure $next)
    {
        // تعیین نام پنل از segment اول URL
        config(['session.connection' => 'manage']);
        config(['session.cookie' => 'known_as_'.'manage']);

        return $next($request);
    }
}
