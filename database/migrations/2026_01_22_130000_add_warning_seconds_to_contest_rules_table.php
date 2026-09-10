<?php
/**
 * Archivo: database/migrations/2026_01_22_130000_add_warning_seconds_to_contest_rules_table.php
 *
 * Migración: agrega configuración de aviso de tiempo (X segundos).
 *
 * - warning_seconds: cuando falten X segundos, el sistema muestra aviso (UI) y permite lógica coherente.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            // Aviso único (segundos) para "faltan X segundos"
            if (!Schema::hasColumn('contest_rules', 'warning_seconds')) {
                $table->unsignedInteger('warning_seconds')->default(10)->after('seconds_per_question');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (Schema::hasColumn('contest_rules', 'warning_seconds')) {
                $table->dropColumn('warning_seconds');
            }
        });
    }
};
