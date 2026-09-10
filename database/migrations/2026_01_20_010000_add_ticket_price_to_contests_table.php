<?php
/**
 * Archivo: database/migrations/2026_01_20_010000_add_ticket_price_to_contests_table.php
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
            if (!Schema::hasColumn('contests', 'ticket_price')) {
                $table->decimal('ticket_price', 10, 2)->default(0)->after('status');
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            if (Schema::hasColumn('contests', 'ticket_price')) {
                $table->dropColumn('ticket_price');
            }
        });
    }
};
