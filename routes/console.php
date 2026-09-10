<?php
/**
 * Archivo: routes/console.php
 *
 * Archivo de rutas: define endpoints web y middleware.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
