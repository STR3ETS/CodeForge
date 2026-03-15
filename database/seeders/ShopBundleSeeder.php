<?php

namespace Database\Seeders;

use App\Models\ShopBundle;
use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class ShopBundleSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = [
            [
                'slug' => 'bundle-vuur',
                'name' => 'Vuur Set',
                'description' => 'Vlammend van kop tot teen. Border, effect & naam in vuurthema.',
                'rarity' => 'legendary',
                'icon' => 'fa-solid fa-fire',
                'price' => 1100,
                'level_required' => 12,
                'items' => ['border-flame', 'effect-fire-ring', 'name-fire'],
            ],
            [
                'slug' => 'bundle-oceaan',
                'name' => 'Oceaan Set',
                'description' => 'Duik in de diepzee met koele blauwe cosmetics.',
                'rarity' => 'rare',
                'icon' => 'fa-solid fa-water',
                'price' => 275,
                'level_required' => 3,
                'items' => ['border-ocean-blue', 'effect-snowflakes', 'name-ocean'],
            ],
            [
                'slug' => 'bundle-neon',
                'name' => 'Neon Set',
                'description' => 'Cyberpunk vibes met pulserende neon kleuren.',
                'rarity' => 'epic',
                'icon' => 'fa-solid fa-bolt',
                'price' => 600,
                'level_required' => 8,
                'items' => ['border-neon-pulse', 'effect-neon-rings', 'name-neon-pulse'],
            ],
            [
                'slug' => 'bundle-schaduw',
                'name' => 'Schaduw Set',
                'description' => 'Mysterieus en donker. Verberg je in de schaduwen.',
                'rarity' => 'legendary',
                'icon' => 'fa-solid fa-moon',
                'price' => 1200,
                'level_required' => 14,
                'items' => ['border-void', 'effect-shadow-aura', 'name-void', 'hat-shadow-hood'],
            ],
            [
                'slug' => 'bundle-koninklijk',
                'name' => 'Koninklijke Set',
                'description' => 'Heers als een koning met diamanten en goud.',
                'rarity' => 'legendary',
                'icon' => 'fa-solid fa-crown',
                'price' => 1000,
                'level_required' => 12,
                'items' => ['border-diamond', 'effect-golden-aura', 'name-gold-gradient', 'hat-crown'],
            ],
            [
                'slug' => 'bundle-natuur',
                'name' => 'Natuur Set',
                'description' => 'Eén met de natuur. Bladeren, bossen en groene vibes.',
                'rarity' => 'rare',
                'icon' => 'fa-solid fa-leaf',
                'price' => 300,
                'level_required' => 3,
                'items' => ['border-emerald', 'effect-leaves', 'name-forest', 'hat-mushroom'],
            ],
            [
                'slug' => 'bundle-galaxy',
                'name' => 'Galaxy Set',
                'description' => 'Reis door de kosmos met sterrenstelsels en noorderlicht.',
                'rarity' => 'legendary',
                'icon' => 'fa-solid fa-meteor',
                'price' => 1300,
                'level_required' => 15,
                'items' => ['border-supernova', 'effect-galaxy', 'name-galaxy', 'hat-galaxy-helmet'],
            ],
            [
                'slug' => 'bundle-giftig',
                'name' => 'Toxic Set',
                'description' => 'Radioactief groen van top tot teen. Pas op!',
                'rarity' => 'epic',
                'icon' => 'fa-solid fa-biohazard',
                'price' => 550,
                'level_required' => 8,
                'items' => ['border-toxic', 'effect-toxic-cloud', 'name-toxic-gradient'],
            ],
            [
                'slug' => 'bundle-ijs',
                'name' => 'IJs Set',
                'description' => 'Bevroren schoonheid. Ijskoud en kristalhelder.',
                'rarity' => 'epic',
                'icon' => 'fa-solid fa-snowflake',
                'price' => 650,
                'level_required' => 8,
                'items' => ['border-frost', 'effect-ice-shards', 'name-ice', 'hat-ice-crown'],
            ],
            [
                'slug' => 'bundle-sakura',
                'name' => 'Sakura Set',
                'description' => 'Japanse kersenbloesem in volle bloei.',
                'rarity' => 'rare',
                'icon' => 'fa-solid fa-fan',
                'price' => 350,
                'level_required' => 4,
                'items' => ['border-rose', 'effect-cherry-blossom', 'name-pink', 'flair-chill'],
            ],
        ];

        foreach ($bundles as $bundleData) {
            $itemSlugs = $bundleData['items'];
            unset($bundleData['items']);

            $bundle = ShopBundle::updateOrCreate(
                ['slug' => $bundleData['slug']],
                $bundleData
            );

            $itemIds = ShopItem::whereIn('slug', $itemSlugs)->pluck('id');
            $bundle->items()->sync($itemIds);
        }
    }
}
