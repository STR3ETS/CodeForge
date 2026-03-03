<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Game/account stats
            $table->string('plan')->default('free')->after('password'); // free | pro (later uitbreiden)
            $table->unsignedInteger('xp')->default(0)->after('plan');
            $table->unsignedInteger('streak')->default(0)->after('xp');

            // Daily challenge limit tracking
            $table->unsignedSmallInteger('daily_challenges_done')->default(0)->after('streak');
            $table->date('daily_challenges_date')->nullable()->after('daily_challenges_done');

            $table->timestamp('last_challenge_completed_at')->nullable()->after('daily_challenges_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'xp',
                'streak',
                'daily_challenges_done',
                'daily_challenges_date',
                'last_challenge_completed_at',
            ]);
        });
    }
};