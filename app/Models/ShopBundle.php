<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBundle extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'rarity', 'icon',
        'price', 'level_required', 'active',
    ];

    protected $casts = [
        'price' => 'integer',
        'level_required' => 'integer',
        'active' => 'boolean',
    ];

    public function items()
    {
        return $this->belongsToMany(ShopItem::class, 'shop_bundle_items');
    }
}
