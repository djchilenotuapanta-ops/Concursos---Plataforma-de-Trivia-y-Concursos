<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {

            $table->unsignedInteger('attempts_used')
                ->default(0)
                ->after('status');

            $table->timestamp('attempt_started_at')
                ->nullable()
                ->after('joined_at');

            $table->timestamp('attempt_expires_at')
                ->nullable()
                ->after('attempt_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->dropColumn([
                'attempts_used',
                'attempt_started_at',
                'attempt_expires_at'
            ]);
        });
    }
};
