<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */

protected $middlewareGroups = [

    'api' => [
        'throttle:api',
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ],
];

protected $routeMiddleware = [
 'admin' => \App\Http\Middleware\AdminMiddleware::class,


];


}
