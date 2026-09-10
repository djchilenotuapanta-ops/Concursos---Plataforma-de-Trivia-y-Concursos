<?php
/**
 * Archivo: database/migrations/2026_01_20_000030_add_only_adults_to_contest_rules_table.php
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
            if (!Schema::hasColumn('contest_rules', 'only_adults')) {
                $table->boolean('only_adults')->default(false)->after('unique_by_cedula');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (Schema::hasColumn('contest_rules', 'only_adults')) {
                $table->dropColumn('only_adults');
            }
        });
    }
};
