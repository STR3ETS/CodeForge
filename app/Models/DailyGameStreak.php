<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyGameStreak extends Model
{
    protected $fillable = [
        'user_id',
        'game_key',
        'current_streak',
        'best_streak',
        'last_solved_date',
        'jokers',
    ];

    protected $casts = [
        'last_solved_date' => 'date',
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'jokers' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}