<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // تنظيف null bytes من جميع الطلبات (يجب أن يكون أول middleware)
        $middleware->prepend(\App\Http\Middleware\SanitizeNullBytes::class);
        
        // تعطيل CSRF protection لـ جميع API routes
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // تسجيل Admin Middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
