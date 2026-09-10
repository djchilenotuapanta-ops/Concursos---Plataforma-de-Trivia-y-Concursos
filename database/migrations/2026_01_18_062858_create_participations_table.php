<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('participations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('tickets')->default(1);

            $table->string('status')->default('active')->index();

            $table->string('cedula_snapshot')->nullable()->index();

            $table->dateTime('joined_at');

            $table->timestamps();

            $table->unique(['contest_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
