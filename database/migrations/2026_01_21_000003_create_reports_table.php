<?php
/**
 * Archivo: database/migrations/2026_01_21_000003_create_reports_table.php
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
        if (Schema::hasTable('reports')) {
            return;
        }

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contest_id')->nullable()->constrained('contests')->nullOnDelete();

            $table->string('reason')->default('contenido inapropiado');
            $table->text('message');

            // open | reviewed | closed
            $table->string('status')->default('open')->index();

            $table->timestamps();
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
