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

    // ===================================================
    // === YENİ PROVIDER'I (YAYINLAMA) BURAYA EKLİYORUZ ===
    ->withProviders([
        App\Providers\BroadcastServiceProvider::class,
    ])
    // ===================================================

    ->withMiddleware(function (Middleware $middleware) {
        
        // SİZİN MEVCUT SPATIE AYARLARINIZ (KORUNDU)
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\RoleMiddleware::class, // DİKKAT: Burası RoleMiddleware'a işaret ediyor, PermissionMiddleware olmalı.
            // ÖNERİLEN DÜZELTME:
            // 'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();