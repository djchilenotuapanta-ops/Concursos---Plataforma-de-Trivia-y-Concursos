<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            if (!Schema::hasColumn('participations', 'ending_soon_notified_attempt')) {
                $table->unsignedInteger('ending_soon_notified_attempt')->nullable()->after('attempt_expires_at');
            }
            if (!Schema::hasColumn('participations', 'ending_soon_notified_at')) {
                $table->timestamp('ending_soon_notified_at')->nullable()->after('ending_soon_notified_attempt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            if (Schema::hasColumn('participations', 'ending_soon_notified_at')) {
                $table->dropColumn('ending_soon_notified_at');
            }
            if (Schema::hasColumn('participations', 'ending_soon_notified_attempt')) {
                $table->dropColumn('ending_soon_notified_attempt');
            }
        });
    }
};
