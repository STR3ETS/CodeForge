<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description');
            $table->string('rarity')->default('rare'); // for styling
            $table->string('icon')->default('fa-solid fa-box');
            $table->integer('price'); // discounted bundle price
            $table->integer('level_required')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('shop_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_bundle_id')->constrained('shop_bundles')->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained('shop_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_bundle_items');
        Schema::dropIfExists('shop_bundles');
    }
};
