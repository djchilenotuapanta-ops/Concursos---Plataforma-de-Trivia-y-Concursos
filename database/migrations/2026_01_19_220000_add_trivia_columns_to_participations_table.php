<?php
/**
 * Archivo: database/migrations/2026_01_19_220000_add_trivia_columns_to_participations_table.php
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
            // Trivia: conteo de intentos y control de tiempo total
            if (!Schema::hasColumn('participations', 'attempts_used')) {
                $table->unsignedInteger('attempts_used')->default(0)->after('joined_at');
            }
            if (!Schema::hasColumn('participations', 'attempt_started_at')) {
                $table->dateTime('attempt_started_at')->nullable()->after('attempts_used');
            }
            if (!Schema::hasColumn('participations', 'attempt_expires_at')) {
                $table->dateTime('attempt_expires_at')->nullable()->index()->after('attempt_started_at');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            if (Schema::hasColumn('participations', 'attempt_expires_at')) {
                $table->dropColumn('attempt_expires_at');
            }
            if (Schema::hasColumn('participations', 'attempt_started_at')) {
                $table->dropColumn('attempt_started_at');
            }
            if (Schema::hasColumn('participations', 'attempts_used')) {
                $table->dropColumn('attempts_used');
            }
        });
    }
};
