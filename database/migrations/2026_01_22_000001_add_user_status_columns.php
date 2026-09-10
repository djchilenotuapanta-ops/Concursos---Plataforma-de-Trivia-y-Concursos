<?php
/**
 * Archivo: database/migrations/2026_01_22_000001_add_user_status_columns.php
 *
 * Este proyecto usa campos adicionales en users (active, desactivación, birthdate,
 * last_login_at, etc.). Si la tabla users fue creada sin esos campos, al crear
 * admins/empresas desde tinker o desde el panel se produce un error SQL.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Relación opcional (por si en algún flujo se vincula un usuario a una empresa)
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->index()->after('role');
            }

            // 0 = inactivo, 1 = activo
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(1)->index()->after('role');
            }

            // Auditoría de desactivación
            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('active');
            }
            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->string('deactivation_reason')->nullable()->after('deactivated_at');
            }
            if (!Schema::hasColumn('users', 'deactivated_by')) {
                $table->unsignedBigInteger('deactivated_by')->nullable()->index()->after('deactivation_reason');
            }

            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('cedula');
            }

            // Métrica de último ingreso
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->index()->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['company_id','active','deactivated_at','deactivation_reason','deactivated_by','birthdate','last_login_at'] as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $cols[] = $c;
                }
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
