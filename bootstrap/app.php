<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;
use App\Events\DuplicateFundWarning;
use App\Listeners\HandleDuplicateFundWarning;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {})
    ->booting(function ($app) {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\DuplicateFundWarning::class,
            \App\Listeners\HandleDuplicateFundWarning::class
        );
    })
    ->create();
