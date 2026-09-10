<?php
/**
 * Archivo: public/index.php
 *
 * Archivo PHP del proyecto.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

define('LARAVEL_START', microtime(true));

// Modo mantenimiento
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload de Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap de Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Crear kernel para manejar la solicitud HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Capturar la solicitud y generar la respuesta
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Enviar la respuesta al navegador
$response->send();

// Terminar la solicitud
$kernel->terminate($request, $response);
