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
    ->withMiddleware(function (Middleware $middleware) {
        // ⬇️ HIER ALIASE REGISTRIEREN
        $middleware->alias([
            'adminsecret' => \App\Http\Middleware\AdminSecret::class,
            'guidauth'    => \App\Http\Middleware\GuidAuth::class,
        ]);
        // Optional: globale oder Gruppen-Middleware könntest du hier auch setzen
        // $middleware->append(\App\Http\Middleware\Something::class);
        // $middleware->web([\App\Http\Middleware\Example::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
