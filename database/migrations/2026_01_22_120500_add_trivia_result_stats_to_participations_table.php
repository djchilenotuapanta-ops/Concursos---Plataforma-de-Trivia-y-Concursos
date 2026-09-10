<?php
/**
 * Archivo: database/migrations/2026_01_22_120500_add_trivia_result_stats_to_participations_table.php
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
            if (!Schema::hasColumn('participations', 'last_result_attempt')) {
                $table->unsignedInteger('last_result_attempt')->default(0)->after('last_notified_attempt');
            }
            if (!Schema::hasColumn('participations', 'last_correct')) {
                $table->unsignedInteger('last_correct')->default(0)->after('last_result_attempt');
            }
            if (!Schema::hasColumn('participations', 'last_wrong')) {
                $table->unsignedInteger('last_wrong')->default(0)->after('last_correct');
            }
            if (!Schema::hasColumn('participations', 'last_duration_seconds')) {
                $table->unsignedInteger('last_duration_seconds')->nullable()->after('last_wrong');
            }
            if (!Schema::hasColumn('participations', 'last_finished_at')) {
                $table->dateTime('last_finished_at')->nullable()->after('last_duration_seconds');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $cols = [];
            foreach (['last_finished_at','last_duration_seconds','last_wrong','last_correct','last_result_attempt'] as $c) {
                if (Schema::hasColumn('participations', $c)) $cols[] = $c;
            }
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
