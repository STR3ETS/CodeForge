<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuestClaim extends Model
{
    protected $fillable = [
        'user_id',
        'quest_key',
        'quest_date',
        'reward_xp',
        'claimed_at',
    ];

    protected $casts = [
        'quest_date' => 'date',
        'claimed_at' => 'datetime',
    ];
}