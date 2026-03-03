<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_game_runs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('game_key', 64);      // e.g. word-forge
            $table->date('puzzle_date');         // today's date

            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            $table->boolean('solved')->default(false);
            $table->unsignedTinyInteger('attempts')->default(0);

            $table->json('state')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'game_key', 'puzzle_date']);
            $table->index(['game_key', 'puzzle_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_game_runs');
    }
};