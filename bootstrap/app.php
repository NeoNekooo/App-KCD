<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckMenuAccess;
use App\Providers\SidebarServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        SidebarServiceProvider::class,
    ])
    
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // PERBAIKAN DISINI:
        // Ganti 'menu.access' jadi 'check_menu' biar cocok sama routes/web.php
        $middleware->alias([
            'check_menu' => CheckMenuAccess::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();