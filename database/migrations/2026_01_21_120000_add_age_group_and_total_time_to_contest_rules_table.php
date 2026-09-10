<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_rules', 'age_group')) {
                $table->string('age_group', 20)->default('all')->after('only_adults');
            }

            if (!Schema::hasColumn('contest_rules', 'total_time_minutes')) {
                $table->unsignedInteger('total_time_minutes')->nullable()->after('attempts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (Schema::hasColumn('contest_rules', 'age_group')) {
                $table->dropColumn('age_group');
            }

            if (Schema::hasColumn('contest_rules', 'total_time_minutes')) {
                $table->dropColumn('total_time_minutes');
            }
        });
    }
};
