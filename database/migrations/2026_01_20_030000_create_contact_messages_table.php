<?php
/**
 * Archivo: database/migrations/2026_01_20_030000_create_contact_messages_table.php
 *
 * Migración: crea o modifica tablas/columnas en la base de datos.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla: contact_messages
 *
 * Guarda mensajes enviados desde la landing (formulario de contacto).
 * El admin los revisa desde su panel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // Datos del remitente
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();

            // Contenido del mensaje
            $table->text('message');

            // Datos técnicos (útiles para seguridad y auditoría)
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Control de lectura
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Índice: listados más rápidos
            $table->index(['read_at', 'created_at']);
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
