<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Use-Statement für Middleware hinzufügen

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { // <<< HIER

        // Hier werden globale Middleware, Gruppen und Aliase konfiguriert

        // Middleware-Alias hinzufügen
        $middleware->alias([ // <<< ALIAS HIER REGISTRIEREN
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // Füge hier den Alias für deine Rollen-Middleware hinzu
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            // Weitere Aliase von Breeze/Standard-Laravel könnten hier auch sein
            // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Beispiel
            // 'auth' => \App\Http\Middleware\Authenticate::class, // Beispiel
        ]);

        // Beispiel: Hinzufügen von Middleware zu einer Gruppe (z.B. 'web')
        // $middleware->web(append: [
        //     \App\Http\Middleware\ExampleMiddleware::class,
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling
    })->create();