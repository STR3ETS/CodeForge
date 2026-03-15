<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');          // border, hat, effect, badge_flair
            $table->string('rarity');        // common, rare, epic, legendary
            $table->unsignedInteger('price');
            $table->string('css_class')->nullable();     // CSS class or value applied
            $table->string('preview_image')->nullable();  // preview path
            $table->unsignedInteger('level_required')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_cosmetics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained('shop_items')->cascadeOnDelete();
            $table->boolean('equipped')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'shop_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cosmetics');
        Schema::dropIfExists('shop_items');
    }
};
