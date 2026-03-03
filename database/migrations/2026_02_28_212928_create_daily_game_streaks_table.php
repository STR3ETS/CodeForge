<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_game_streaks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game_key', 80);

            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('best_streak')->default(0);

            $table->date('last_solved_date')->nullable();

            // “Jokers”/freezes (zoals in screenshot)
            $table->unsignedInteger('jokers')->default(2);

            $table->timestamps();

            $table->unique(['user_id', 'game_key']);
            $table->index(['game_key', 'last_solved_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_game_streaks');
    }
};