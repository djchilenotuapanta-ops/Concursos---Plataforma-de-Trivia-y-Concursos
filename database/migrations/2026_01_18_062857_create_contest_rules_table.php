<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contest_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedInteger('max_tickets_per_user')->nullable();
            $table->boolean('unique_by_cedula')->default(false);
            $table->unsignedInteger('eliminated_before_prize')->default(0);

            $table->unsignedInteger('seconds_per_question')->nullable();
            $table->unsignedInteger('attempts')->nullable();
            $table->unsignedInteger('expires_after_join_minutes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_rules');
    }
};
