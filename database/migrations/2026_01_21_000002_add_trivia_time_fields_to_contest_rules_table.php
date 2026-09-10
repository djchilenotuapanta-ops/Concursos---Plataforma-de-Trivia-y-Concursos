<?php
/**
 * Archivo: database/migrations/2026_01_21_000002_add_trivia_time_fields_to_contest_rules_table.php
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
        Schema::table('contest_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_rules', 'trivia_questions_count')) {
                $table->unsignedInteger('trivia_questions_count')->nullable()->after('seconds_per_question');
            }
            if (!Schema::hasColumn('contest_rules', 'trivia_total_seconds')) {
                $table->unsignedInteger('trivia_total_seconds')->nullable()->after('trivia_questions_count');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (Schema::hasColumn('contest_rules', 'trivia_total_seconds')) {
                $table->dropColumn('trivia_total_seconds');
            }
            if (Schema::hasColumn('contest_rules', 'trivia_questions_count')) {
                $table->dropColumn('trivia_questions_count');
            }
        });
    }
};
