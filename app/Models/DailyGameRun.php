<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyGameRun extends Model
{
    protected $fillable = [
        'user_id',
        'game_key',
        'puzzle_date',
        'started_at',
        'finished_at',
        'duration_ms',
        'solved',
        'attempts',
        'state',
    ];

    protected $casts = [
        'puzzle_date' => 'date',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'solved'      => 'boolean',
        'attempts'    => 'integer',
        'duration_ms' => 'integer',
        'state'       => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}