<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // ✅ THIS PART WAS MISSING (VERY IMPORTANT)
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    // ✅ Your middleware alias (THIS IS SAFE)
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'checkRole' => CheckRole::class,
        ]);
    })

    ->create();
