<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_quest_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('quest_key', 64);
            $table->date('quest_date');
            $table->unsignedInteger('reward_xp')->default(0);
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'quest_key', 'quest_date'], 'uq_dqc_user_key_date');
            $table->index(['user_id', 'quest_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quest_claims');
    }
};