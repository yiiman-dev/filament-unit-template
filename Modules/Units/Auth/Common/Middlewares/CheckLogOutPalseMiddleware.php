<?php

namespace Units\Auth\Common\Middlewares;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CheckLogOutPalseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!filament()->auth()->guest()) {
            $redirect_url = Filament::getPanel('my')->getUrl() . '/';
            $key = filament()->getTenant()->national_code . '_logout';
            if (cache()->has($key)) {
                cache()->delete($key);
                Filament::auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                header('Location: /my');
                exit;
            }
            $key = filament()->auth()->id() . '_logout';
            if (cache()->has($key)) {
                cache()->delete($key);
                Filament::auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                header('Location: /my');
                exit;
            }
        }

        return $next($request);
    }
}
