<?php

namespace Units\Panels\Admin\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Kernel برای پنل ادمین
 */
class Kernel extends HttpKernel
{
    /**
     * Middleware‌های عمومی
     *
     * @var array
     */
    protected $middleware = [
        \Units\Panels\Admin\Http\Middleware\APIAuthMiddleware::class,
    ];

    /**
     * گروه‌های Middleware
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Units\Panels\Admin\Http\Middleware\APIAuthMiddleware::class,
        ],

        'api' => [
            \Units\Panels\Admin\Http\Middleware\APIAuthMiddleware::class,
        ],
    ];

    /**
     * Middleware‌های نام‌گذاری شده
     *
     * @var array
     */
    protected $routeMiddleware = [
        'api.auth' => \Units\Panels\Admin\Http\Middleware\APIAuthMiddleware::class,
    ];
}
