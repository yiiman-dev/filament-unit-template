<?php

namespace Units\Panels\Common\Middlewares;

use Closure;
use Illuminate\Http\Request;

class FilamentPanelsMiddleware
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
        $content = json_decode($request->getContent(), true);
        if ($content) {
            $raw_snapshot = $content['components'][0]['snapshot'];
            $snapshot = json_decode($raw_snapshot, true);
            $panel = str($snapshot['memo']['path'])->explode('/')->first();
            switch (true) {
                case $panel == 'manage':
                case $panel == 'admin':
                case $panel == 'my':
                    config(['session.connection' => $panel]);
                    config(['session.cookie' => 'known_as_' . $panel]);
            }
        }

        switch (true) {
            case str($request->path())->startsWith('my'):
                config(['session.connection' => 'my']);
                config(['session.cookie' => 'known_as_' . 'my']);
                break;
//            case str($request->path())->start('manage'):
//                config(['session.connection' => 'manage']);
//                config(['session.cookie' => 'known_as_' . 'manage']);
//                break;
//             case str($request->path())->start('admin'):
//                config(['session.connection' => 'admin']);
//                config(['session.cookie' => 'known_as_' . 'admin']);
//                break;

        }
        return $next($request);
    }
}
