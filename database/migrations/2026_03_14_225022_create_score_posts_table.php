<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game_key', 50);
            $table->string('game_name', 100);
            $table->date('puzzle_date');
            $table->boolean('solved')->default(true);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedInteger('attempts')->nullable();
            $table->string('formatted_time', 10)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'game_key', 'puzzle_date']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_posts');
    }
};
