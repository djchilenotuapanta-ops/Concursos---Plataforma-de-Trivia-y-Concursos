<?php
/**
 * Archivo: database/migrations/2026_01_24_120000_add_audience_to_contest_questions_table.php
 *
 * Migración: agrega la columna audience a contest_questions.
 *
 * Nota: Valores permitidos:
 * - all   : para todos
 * - adult : solo 18+
 * - kid   : solo niños
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contest_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_questions', 'audience')) {
                $table->string('audience')->default('all')->after('correct_option')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('contest_questions', function (Blueprint $table) {
            if (Schema::hasColumn('contest_questions', 'audience')) {
                $table->dropColumn('audience');
            }
        });
    }
};
