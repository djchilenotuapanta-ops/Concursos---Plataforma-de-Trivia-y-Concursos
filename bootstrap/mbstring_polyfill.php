<?php

/**
 * mbstring polyfill (solo lo mínimo necesario)
 *
 * Algunas instalaciones locales (XAMPP/WAMP) pueden venir con mbstring deshabilitado
 * y eso provoca Error 500/CLI al arrancar Laravel 12 (por ejemplo, Str::studly usa mb_split).
 *
 * ✅ Recomendado: habilitar/extender mbstring en php.ini.
 * ✅ Mientras tanto: este polyfill evita el fallo fatal y permite trabajar.
 */

if (!function_exists('mb_split')) {
    /**
     * Polyfill básico de mb_split.
     * mb_split recibe un patrón SIN delimitadores (ej: "\s+")
     * y devuelve un array de partes.
     */
    function mb_split(string $pattern, string $string, int $limit = -1): array
    {
        $regex = '/' . str_replace('/', '\\/', $pattern) . '/u';
        $parts = preg_split($regex, $string, $limit === -1 ? -1 : $limit);
        return is_array($parts) ? $parts : [$string];
    }
}
