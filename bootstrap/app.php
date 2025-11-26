<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Redirect authenticated users to dashboard
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/dashboard');
        
        // Exclude Cypress proxy and bookmarklet from CSRF verification (needs to accept external requests)
        $middleware->validateCsrfTokens(except: [
            'cypress/proxy',
            'cypress/proxy/*',
            'cypress/capture-event-bookmarklet'
        ]);
        
        // Register Spatie Permission middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
