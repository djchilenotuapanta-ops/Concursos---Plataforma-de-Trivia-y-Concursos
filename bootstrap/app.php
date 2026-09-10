<?php
/**
 * Archivo: bootstrap/app.php
 *
 * Archivo PHP del proyecto.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

// ✅ Evita Error 500 por mbstring deshabilitado (...)
// (Laravel 12 usa funciones mb_* al arrancar; en XAMPP/WAMP a veces vienen desactivadas)
require_once __DIR__ . '/mbstring_polyfill.php';

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

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'company.approved' => \App\Http\Middleware\CompanyApproved::class,
            'company.active' => \App\Http\Middleware\CompanyActive::class,
            'company.profile' => \App\Http\Middleware\EnsureCompanyProfileCompleted::class,
            'moderator.backup' => \App\Http\Middleware\ModeratorBackupGate::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
