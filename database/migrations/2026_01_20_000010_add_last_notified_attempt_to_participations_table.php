<?php
/**
 * Archivo: database/migrations/2026_01_20_000010_add_last_notified_attempt_to_participations_table.php
 *
 * Migración: crea o modifica tablas/columnas en la base de datos.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Método: up() - lógica de este archivo.
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            if (!Schema::hasColumn('participations', 'last_notified_attempt')) {
                $table->unsignedInteger('last_notified_attempt')->default(0)->after('attempt_expires_at');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            if (Schema::hasColumn('participations', 'last_notified_attempt')) {
                $table->dropColumn('last_notified_attempt');
            }
        });
    }
};
