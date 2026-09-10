<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_rules', 'eligible_top_n')) {
                $table->unsignedInteger('eligible_top_n')->default(10)->after('age_group');
            }

            if (!Schema::hasColumn('contest_rules', 'winners_count')) {
                $table->unsignedInteger('winners_count')->default(1)->after('eligible_top_n');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contest_rules', function (Blueprint $table) {
            if (Schema::hasColumn('contest_rules', 'winners_count')) {
                $table->dropColumn('winners_count');
            }
            if (Schema::hasColumn('contest_rules', 'eligible_top_n')) {
                $table->dropColumn('eligible_top_n');
            }
        });
    }
};
