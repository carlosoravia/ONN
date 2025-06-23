<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\Middleware\ShareErrorsFromSession;
use Illuminate\Session\Middleware\Authenticate;
use App\http\Middleware\AdminAccess;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\Illuminate\Session\Middleware\StartSession::class);
        $middleware->append(\Illuminate\View\Middleware\ShareErrorsFromSession::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
