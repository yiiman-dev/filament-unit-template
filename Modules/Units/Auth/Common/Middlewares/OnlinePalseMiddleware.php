<?php

namespace Units\Auth\Common\Middlewares;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OnlinePalseMiddleware
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
    public function handle(Request $request, Closure $next){
        cache()->set(
            filament()->getTenant()->national_code.'_online'
            ,'true',20);
        cache()->set(
            filament()->auth()->id().'_online'
            ,'true',20);
        return $next($request);
    }
}
