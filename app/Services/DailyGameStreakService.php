<?php

namespace App\Services;

use App\Models\DailyGameRun;
use App\Models\DailyGameStreak;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DailyGameStreakService
{
    public function recordSolved(User $user, string $gameKey, Carbon $date): DailyGameStreak
    {
        $day = $date->copy()->startOfDay()->toDateString();

        $streak = DailyGameStreak::firstOrCreate(
            ['user_id' => $user->id, 'game_key' => $gameKey],
            ['current_streak' => 0, 'best_streak' => 0, 'last_solved_date' => null, 'jokers' => 2]
        );

        // al verwerkt voor vandaag
        if ($streak->last_solved_date && $streak->last_solved_date->toDateString() === $day) {
            return $streak;
        }

        $yesterday = $date->copy()->subDay()->toDateString();

        if (!$streak->last_solved_date) {
            $streak->current_streak = 1;
        } else {
            $last = $streak->last_solved_date->toDateString();

            if ($last === $yesterday) {
                $streak->current_streak = (int)$streak->current_streak + 1;
            } else {
                // gat -> reset (later kun je hier jokers inzetten)
                $streak->current_streak = 1;
            }
        }

        $streak->last_solved_date = $day;
        $streak->best_streak = max((int)$streak->best_streak, (int)$streak->current_streak);

        $streak->save();

        return $streak;
    }

    public function uiPayload(User $user, string $gameKey, Carbon $today, int $daysBack = 6): array
    {
        $today = $today->copy()->startOfDay();
        $from = $today->copy()->subDays($daysBack);

        $streak = DailyGameStreak::firstOrCreate(
            ['user_id' => $user->id, 'game_key' => $gameKey],
            ['current_streak' => 0, 'best_streak' => 0, 'last_solved_date' => null, 'jokers' => 2]
        );

        $solvedMap = DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', $gameKey)
            ->where('solved', true)
            ->whereBetween('puzzle_date', [$from->toDateString(), $today->toDateString()])
            ->pluck('puzzle_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip()
            ->all();

        $nl = ['zo','ma','di','wo','do','vr','za'];

        $days = [];
        $cursor = $from->copy();
        while ($cursor->lte($today)) {
            $ds = $cursor->toDateString();
            $days[] = [
                'date' => $ds,
                'dow' => $nl[$cursor->dayOfWeek],
                'is_today' => $ds === $today->toDateString(),
                'solved' => array_key_exists($ds, $solvedMap),
            ];
            $cursor->addDay();
        }

        return [
            'game_key' => $gameKey,
            'current' => (int) $streak->current_streak,
            'best' => (int) $streak->best_streak,
            'jokers' => (int) $streak->jokers,
            'days' => $days,
        ];
    }

    public function uiPayloadGlobal($user, Carbon $today): array
    {
        $today = $today->copy()->startOfDay();

        // ✅ pak de juiste date-kolom automatisch (verschilt vaak per project)
        $dateCol = $this->guessStreakDateColumn();

        // ✅ unieke dagen waarop de user "iets" heeft in daily_game_streaks (ongeacht game_key)
        $dates = DB::table('daily_game_streaks')
            ->where('user_id', $user->id)
            ->whereNotNull($dateCol)
            ->distinct()
            ->pluck($dateCol)
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sort()
            ->values()
            ->all();

        $set = array_flip($dates); // snelle lookup

        // ✅ current streak: alleen als vandaag aanwezig is
        $current = 0;
        $cursor = $today->toDateString();
        while (isset($set[$cursor])) {
            $current++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        // ✅ best streak: langste aaneengesloten reeks
        $best = 0;
        $run = 0;
        $prev = null;

        foreach ($dates as $ds) {
            if ($prev === null) {
                $run = 1;
            } else {
                $prevDay = Carbon::parse($prev);
                $curDay  = Carbon::parse($ds);
                $run = $prevDay->addDay()->toDateString() === $curDay->toDateString() ? ($run + 1) : 1;
            }
            $best = max($best, $run);
            $prev = $ds;
        }

        // ✅ 7-day UI row (laatste 7 dagen incl vandaag)
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $ds = $d->toDateString();

            $days[] = [
                'dow' => strtoupper($d->format('D')), // MON/TUE/... (jij toont uppercase)
                'solved' => isset($set[$ds]),         // hier is solved = "heeft record die dag"
                'is_today' => $i === 0,
            ];
        }

        return [
            'current' => $current,
            'best' => $best,
            'jokers' => 0, // global jokers niet nodig -> 0
            'days' => $days,
        ];
    }

    private function guessStreakDateColumn(): string
    {
        // ✅ probeer de meest logische kolomnamen
        $candidates = ['day', 'date', 'streak_date', 'puzzle_date', 'played_on', 'created_at'];

        foreach ($candidates as $col) {
            if (Schema::hasColumn('daily_game_streaks', $col)) {
                return $col;
            }
        }

        // fallback (als schema check faalt)
        return 'day';
    }
}