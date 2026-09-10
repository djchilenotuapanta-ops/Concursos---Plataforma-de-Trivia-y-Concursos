<?php
/**
 * Archivo: config/ganafacil.php
 *
 * Archivo PHP del proyecto.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración general de GANA FÁCIL
    |--------------------------------------------------------------------------
    | Valores por defecto. El administrador puede sobreescribirlos desde
    | el panel (tabla system_settings).
    */

    // Cantidad de elementos a mostrar en rankings (admin)
    'ranking_top_n' => (int) env('GANA_FACIL_RANKING_TOP_N', 5),

    // Máximo permitido para la trivia (segundos totales)
    'trivia_max_total_seconds' => (int) env('GANA_FACIL_TRIVIA_MAX_TOTAL_SECONDS', 3600), // 60 min

    // Permitir crear preguntas manualmente (además de importar Excel/CSV)
    'trivia_manual_questions_enabled' => (bool) env('GANA_FACIL_TRIVIA_MANUAL_QUESTIONS_ENABLED', true),

    // Años máximos hacia el futuro permitidos en fechas (ej: +5 años)
    'max_years_in_future' => (int) env('GANA_FACIL_MAX_YEARS_IN_FUTURE', 5),
];
