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
    // === PROVIDERS ===
    ->withProviders([
        App\Providers\BroadcastServiceProvider::class,
    ])
    // === MIDDLEWARE ===
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Senin Mevcut Spatie Alias Ayarların
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            // Aşağıdaki hatayı da düzelttim (RoleMiddleware yazıyordu, Permission olmalı)
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class, 
        ]);

        // 2. YENİ EKLENEN: UserActivity Middleware'i
        // "web" grubuna ekliyoruz (append) ki her sayfa açılışında çalışsın.
        $middleware->web(append: [
            \App\Http\Middleware\UserActivity::class,
            \App\Http\Middleware\CheckReadOnlyMode::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();