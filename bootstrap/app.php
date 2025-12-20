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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'enquiry.otp' => \App\Http\Middleware\EnquiryOtpCheck::class,
            'can:view-enquiry' => \App\Http\Middleware\CanViewEnquiry::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'hybrid' => \App\Http\Middleware\HybridAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
