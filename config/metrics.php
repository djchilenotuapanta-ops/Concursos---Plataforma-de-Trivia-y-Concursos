<?php
/**
 * Archivo: config/metrics.php
 *
 * Archivo PHP del proyecto.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Configuracion de metricas (negocio)
    |--------------------------------------------------------------------------
    | Ajusta estos valores segun tu proyecto.
    |
    | subscription_fee:
    |   - Valor referencial de ingreso por suscripcion aprobada (empresa).
    |   - Si todavia no manejas pagos reales, esto te permite mostrar ingresos
    |     en el dashboard de forma coherente.
    |
    | cost_rate:
    |   - Porcentaje de costos operativos estimados (0.25 = 25%).
    |   - Se usa para calcular un margen aproximado: margen = ingresos - costos.
    |
    | satisfaction_default:
    |   - Valor mostrado cuando no existen calificaciones.
    */
    'subscription_fee' => (float) env('METRICS_SUBSCRIPTION_FEE', 10.00),
    'cost_rate' => (float) env('METRICS_COST_RATE', 0.25),
    'satisfaction_default' => null,
];
