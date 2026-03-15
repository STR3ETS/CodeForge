<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_cosmetics', function (Blueprint $table) {
            $table->string('custom_value', 30)->nullable()->after('equipped');
        });
    }

    public function down(): void
    {
        Schema::table('user_cosmetics', function (Blueprint $table) {
            $table->dropColumn('custom_value');
        });
    }
};
