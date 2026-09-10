<?php
/**
 * Archivo: database/migrations/2026_01_21_120000_add_company_moderation_fields_to_users_table.php
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
    // Método: up() - lógica de este archivo.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Estado general (0/1). Para empresas: permite inactivar por quejas/reclamos.
            if (!Schema::hasColumn('users', 'active')) {
                $table->tinyInteger('active')->default(1)->after('role')->index();
            }

            // ✅ Opcional: campo para relacionar subcuentas (si existieran) con una empresa.
            //    (para empresa/participante queda null)
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('active')->constrained('users')->nullOnDelete();
            }

            // ✅ Auditoría de inactivación
            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('subscription_end');
            }
            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            }
            if (!Schema::hasColumn('users', 'deactivated_by')) {
                $table->foreignId('deactivated_by')->nullable()->after('deactivation_reason')->constrained('users')->nullOnDelete();
            }
        });
    }

    // Método: down() - lógica de este archivo.
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deactivated_by')) {
                $table->dropConstrainedForeignId('deactivated_by');
            }
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
            $table->dropColumn(['active','deactivated_at','deactivation_reason']);
        });
    }
};
