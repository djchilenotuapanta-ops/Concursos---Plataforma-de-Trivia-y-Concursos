<?php
/**
 * Archivo: database/migrations/2026_01_20_020000_add_image_and_claim_fields_to_prizes_table.php
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
            // Imagen del premio (subida por la empresa)
            if (!Schema::hasColumn('prizes', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            // Flujo de reclamo/entrega
            if (!Schema::hasColumn('prizes', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable();
            }
            if (!Schema::hasColumn('prizes', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            $cols = [];
            foreach (['image_path', 'claimed_at', 'delivered_at'] as $c) {
                if (Schema::hasColumn('prizes', $c)) $cols[] = $c;
            }
            if (count($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
