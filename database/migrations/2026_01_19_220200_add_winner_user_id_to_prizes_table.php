<?php
/**
 * Archivo: database/migrations/2026_01_19_220200_add_winner_user_id_to_prizes_table.php
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
        Schema::table('prizes', function (Blueprint $table) {
            // Ganador (usuario)
            if (!Schema::hasColumn('prizes', 'winner_user_id')) {
                $table->foreignId('winner_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->index();
            }

            // Fecha cuando ganó
            if (!Schema::hasColumn('prizes', 'won_at')) {
                $table->timestamp('won_at')->nullable()->index();
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            if (Schema::hasColumn('prizes', 'won_at')) {
                $table->dropColumn('won_at');
            }
            if (Schema::hasColumn('prizes', 'winner_user_id')) {
                $table->dropConstrainedForeignId('winner_user_id');
            }
        });
    }
};
