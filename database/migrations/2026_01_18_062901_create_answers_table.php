<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contest_question_id')->constrained('contest_questions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('attempt_no')->default(1);
            $table->string('selected_option');
            $table->boolean('is_correct')->default(false);
            $table->dateTime('answered_at');

            $table->timestamps();

            $table->unique(['contest_question_id', 'user_id', 'attempt_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
