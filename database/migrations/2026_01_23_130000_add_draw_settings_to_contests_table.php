<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            if (!Schema::hasColumn('contests', 'draw_enabled')) {
                $table->boolean('draw_enabled')->default(false)->after('winner_method')->index();
            }
            if (!Schema::hasColumn('contests', 'draw_top_n')) {
                $table->unsignedInteger('draw_top_n')->default(10)->after('draw_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $cols = [];
            foreach (['draw_top_n','draw_enabled'] as $c) {
                if (Schema::hasColumn('contests', $c)) $cols[] = $c;
            }
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
