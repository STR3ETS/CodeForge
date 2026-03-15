<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('score_posts', function (Blueprint $table) {
            $table->string('message', 500)->nullable()->after('formatted_time');
            $table->unsignedTinyInteger('percentile')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('score_posts', function (Blueprint $table) {
            $table->dropColumn(['message', 'percentile']);
        });
    }
};
