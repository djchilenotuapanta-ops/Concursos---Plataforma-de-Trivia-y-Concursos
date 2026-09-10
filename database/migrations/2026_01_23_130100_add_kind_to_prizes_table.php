<?php
/**
 * Archivo: database/migrations/2026_01_23_130100_add_kind_to_prizes_table.php
 *
 * Migración: agrega el tipo de premio:
 * - ranking: premio por desempeño (ej. Top 1, Top 2, etc)
 * - draw: premio por sorteo adicional (ej. sorteo entre Top N)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            if (!Schema::hasColumn('prizes', 'kind')) {
                $table->string('kind')->default('ranking')->after('contest_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            if (Schema::hasColumn('prizes', 'kind')) {
                $table->dropColumn('kind');
            }
        });
    }
};
