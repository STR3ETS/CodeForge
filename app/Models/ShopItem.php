<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'type', 'rarity',
        'price', 'css_class', 'preview_image', 'level_required', 'active',
    ];

    protected $casts = [
        'price' => 'integer',
        'level_required' => 'integer',
        'active' => 'boolean',
    ];

    public function owners()
    {
        return $this->belongsToMany(User::class, 'user_cosmetics')
            ->withPivot('equipped')
            ->withTimestamps();
    }
}
