<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScorePost extends Model
{
    protected $fillable = [
        'user_id',
        'game_key',
        'game_name',
        'puzzle_date',
        'solved',
        'duration_ms',
        'attempts',
        'formatted_time',
        'message',
        'percentile',
    ];

    protected $casts = [
        'puzzle_date' => 'date',
        'solved' => 'boolean',
        'duration_ms' => 'integer',
        'attempts' => 'integer',
        'percentile' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
