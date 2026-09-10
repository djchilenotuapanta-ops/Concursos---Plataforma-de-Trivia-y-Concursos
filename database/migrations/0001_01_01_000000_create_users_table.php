<?php
/**
 * Archivo: database/migrations/0001_01_01_000000_create_users_table.php
 *
 * Migración: crea o modifica tablas/columnas en la base de datos.
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =========================
        // USERS
        // =========================
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // =========================
            // USUARIO BÁSICO
            // =========================
            $table->string('name');
            $table->string('email')->unique();
            // ✅ Laravel (y el UserFactory/Seeder por defecto) usan este campo.
            // Si no existe, php artisan migrate:fresh --seed falla con:
            // "table users has no column named email_verified_at".
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Roles: admin | company | user
            $table->string('role')->default('user')->index();

            // Datos generales
            $table->string('cedula')->nullable()->index();
            $table->string('avatar_path')->nullable();

            // =========================
            // DATOS DE EMPRESA
            // =========================
            $table->string('company_name')->nullable();        // nombre comercial
            $table->string('razon_social')->nullable();
            $table->string('ruc')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('representative_name')->nullable();

            // =========================
            // ARCHIVOS
            // =========================
            $table->string('logo')->nullable();
            $table->string('voucher_image')->nullable(); // comprobante de pago

            // =========================
            // ESTADOS (0 / 1)
            // =========================
            $table->boolean('approved')->default(0)->index();         // empresa aprobada
            $table->boolean('voucher_approved')->default(0)->index(); // pago aprobado

            // =========================
            // SUSCRIPCIÓN ($10 / MES)
            // =========================
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable()->index();

            // Motivo de rechazo
            $table->text('rejection_reason')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // =========================
        // PASSWORD RESET
        // =========================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // =========================
        // SESSIONS (NO NEGOCIO)
        // =========================
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
