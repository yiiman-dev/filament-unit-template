<?php

namespace Units\Panels\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware برای اضافه کردن توکن دسترسی به درخواست‌های API
 */
class APIAuthMiddleware
{
    /**
     * پردازش درخواست
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Authorization', 'Bearer ' . config('api.access_token'));

        return $next($request);
    }
}
