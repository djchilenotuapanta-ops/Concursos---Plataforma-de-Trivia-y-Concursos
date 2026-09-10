<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();

            $table->string('type')->index();

            $table->string('title');
            $table->text('description')->nullable();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->string('status')->default('active')->index();

            $table->timestamp('winner_published_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
