<?php

namespace Units\Sessions\Admin\Middlewares;

use Closure;

class AdminSessionMiddleware
{
    public function handle($request, Closure $next)
    {
        // تعیین نام پنل از segment اول URL
        config(['session.connection' => 'admin']);
        config(['session.cookie' => 'known_as_'.'admin']);
        return $next($request);
    }
}
