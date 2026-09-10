<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contest_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_questions', 'image_path')) {
                $table->string('image_path')->nullable()->after('correct_option');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contest_questions', function (Blueprint $table) {
            if (Schema::hasColumn('contest_questions', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
