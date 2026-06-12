<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))

    /*
    |---------------------------------------------------
    | ROUTING
    |---------------------------------------------------
    */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
 
    /*
    |---------------------------------------------------
    | MIDDLEWARE CONFIGURATION
    |---------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        | WEB MIDDLEWARE STACK
        */
        $middleware->web(append: [
            // tambahkan custom middleware web kalau perlu
        ]);

        /*
        | API MIDDLEWARE STACK (jika nanti dipakai)
        */
        $middleware->api(prepend: [
            // contoh: throttle, auth:sanctum dll
        ]);

        /*
        | MIDDLEWARE ALIAS
        */
        $middleware->alias([

            // AUTHORIZATION (ONLY ENTRY POINT)
            'permission' => \App\Auth\Middleware\PermissionMiddleware::class,

        ]);
    })

    /*
    |---------------------------------------------------
    | EXCEPTIONS HANDLING
    |---------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        | Auto JSON response untuk API route
        */
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*')
        );

    })

    ->create();