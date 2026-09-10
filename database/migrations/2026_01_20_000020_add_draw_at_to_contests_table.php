<?php
/**
 * Archivo: database/migrations/2026_01_20_000020_add_draw_at_to_contests_table.php
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
        Schema::table('contests', function (Blueprint $table) {
            if (!Schema::hasColumn('contests', 'draw_at')) {
                $table->dateTime('draw_at')->nullable()->after('end_at')->index();
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            if (Schema::hasColumn('contests', 'draw_at')) {
                $table->dropColumn('draw_at');
            }
        });
    }
};
