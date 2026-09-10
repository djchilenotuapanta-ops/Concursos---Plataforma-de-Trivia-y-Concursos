<?php
/**
 * Archivo: database/migrations/2026_01_19_220100_create_trivia_progress_table.php
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
        Schema::create('trivia_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('current_order')->default(1);
            $table->dateTime('question_started_at')->nullable();
            $table->boolean('finished')->default(false)->index();

            $table->timestamps();

            $table->unique(['contest_id','user_id']);
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::dropIfExists('trivia_progress');
    }
};
