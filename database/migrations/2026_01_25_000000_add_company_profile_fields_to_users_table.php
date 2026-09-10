<?php
/**
 * Archivo: database/migrations/2026_01_25_000000_add_company_profile_fields_to_users_table.php
 *
 * Migración: agrega campos extra para completar el perfil de empresa.
 * - website: sitio web / red social
 * - description: descripción corta
 * - bank_name / bank_account: datos para pago/transferencia (opcional)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('representative_name');
            }
            if (!Schema::hasColumn('users', 'description')) {
                $table->text('description')->nullable()->after('website');
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('description');
            }
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('bank_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Quitamos solo si existen
            if (Schema::hasColumn('users', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('users', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('users', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('users', 'website')) {
                $table->dropColumn('website');
            }
        });
    }
};
