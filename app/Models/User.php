<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'profile_banner',
        'plan',
        'xp',
        'level',
        'streak',
        'daily_challenges_done',
        'daily_challenges_date',
        'last_challenge_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'daily_challenges_date' => 'date',
        'last_challenge_completed_at' => 'datetime',
    ];

    /**
     * Geef XP + update level automatisch.
     * Gebruik straks in je games: auth()->user()->addXp(250);
     */
    public function addXp(int $amount): array
    {
        $amount = max(0, $amount);

        $this->xp = (int) $this->xp + $amount;

        $meta = $this->levelMetaFromXp((int) $this->xp);

        // level opslaan
        $this->level = (int) $meta['level'];

        $this->save();

        return $meta;
    }

    /**
     * Handig om 1x te syncen als xp/level ooit uit sync raakt.
     */
    public function syncLevelFromXpIfNeeded(): void
    {
        $meta = $this->levelMetaFromXp((int) $this->xp);

        if ((int) $this->level !== (int) $meta['level']) {
            $this->level = (int) $meta['level'];
            $this->save();
        }
    }

    /**
     * Meta voor je UI: xp, level, nextXp, percent
     */
    public function levelMeta(): array
    {
        return $this->levelMetaFromXp((int) $this->xp);
    }

    /**
     * Core level logic op basis van config thresholds.
     */
    protected function levelMetaFromXp(int $xp): array
    {
        $thresholds = (array) config('levels.thresholds', []);
        if (empty($thresholds)) {
            $thresholds = [1 => 5000];
        }

        ksort($thresholds);

        $maxKey = (int) max(array_keys($thresholds));
        $maxThreshold = (int) $thresholds[$maxKey];

        // bepaal level (thresholds zijn TOTAL XP grenzen)
        $level = 1;
        foreach ($thresholds as $lvl => $toReachNext) {
            if ($xp >= (int) $toReachNext) {
                $level = (int) $lvl + 1;
                continue;
            }
            break;
        }

        // prev/next threshold (TOTAL XP)
        $prevXp = 0;
        if ($level > 1) {
            $prevXp = (int) ($thresholds[$level - 1] ?? 0); // xp nodig om dit level te bereiken
        }

        // next threshold (TOTAL XP)
        $nextXp = $level > $maxKey
            ? $maxThreshold
            : (int) ($thresholds[$level] ?? $maxThreshold);

        // ✅ progress binnen dit level
        $inLevel = max(0, $xp - $prevXp);
        $nextInLevel = max(1, $nextXp - $prevXp);

        $percentInLevel = (int) round(min(100, ($inLevel / $nextInLevel) * 100));

        return [
            // total
            'xp' => $xp,
            'level' => $level,
            'nextXp' => $nextXp,

            // ✅ extra
            'prevXp' => $prevXp,
            'inLevel' => $inLevel,
            'nextInLevel' => $nextInLevel,

            // ✅ percent moet nu “in level” zijn
            'percent' => $percentInLevel,
        ];
    }
}