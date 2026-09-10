<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            if (!Schema::hasColumn('contests', 'winner_method')) {
                $table->string('winner_method')->default('ranking')->after('ticket_price')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            if (Schema::hasColumn('contests', 'winner_method')) {
                $table->dropColumn('winner_method');
            }
        });
    }
};
