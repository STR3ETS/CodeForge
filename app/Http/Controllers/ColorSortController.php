<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ColorSortController extends Controller
{
    private const GAME_KEY = 'color-sort';
    private const STACK_COUNT = 5;
    private const STACK_HEIGHT = 4;
    private const COLOR_COUNT = 3; // 3 colors × 4 blocks = 12 blocks in 5 stacks (3 filled + 2 empty)

    /**
     * Generate a daily Color Sort puzzle with seeded RNG.
     * 5 stacks, max 4 blocks each, 3 colors (4 blocks per color).
     */
    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);
        $rng = $this->seededRng($seed);

        $colors = ['red', 'blue', 'green'];

        // Create blocks: 4 of each color = 12 blocks
        $blocks = [];
        foreach ($colors as $c) {
            for ($i = 0; $i < self::STACK_HEIGHT; $i++) {
                $blocks[] = $c;
            }
        }

        // Shuffle with seeded RNG (Fisher-Yates)
        for ($i = count($blocks) - 1; $i > 0; $i--) {
            $j = (int) floor($rng() * ($i + 1));
            [$blocks[$i], $blocks[$j]] = [$blocks[$j], $blocks[$i]];
        }

        // Distribute across stacks (first 3 stacks get 4 blocks, 2 empty)
        $stacks = [];
        $idx = 0;
        for ($s = 0; $s < self::STACK_COUNT; $s++) {
            $stacks[$s] = [];
            if ($s < self::COLOR_COUNT) {
                for ($b = 0; $b < self::STACK_HEIGHT; $b++) {
                    $stacks[$s][] = $blocks[$idx++];
                }
            }
        }

        // Ensure it's not already solved (extremely unlikely but check)
        $solved = true;
        foreach ($stacks as $stack) {
            if (empty($stack)) continue;
            if (count(array_unique($stack)) > 1) {
                $solved = false;
                break;
            }
        }
        if ($solved) {
            $tmp = $stacks[0][0];
            $stacks[0][0] = $stacks[1][0];
            $stacks[1][0] = $tmp;
        }

        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'stacks' => $stacks,
            'stack_count' => self::STACK_COUNT,
            'stack_height' => self::STACK_HEIGHT,
            'colors' => $colors,
        ];
    }

    private function seededRng(int $seed): \Closure
    {
        $a = $seed;
        return function () use (&$a) {
            $a = ($a + 0x6D2B79F5) & 0xFFFFFFFF;
            $t = (($a ^ ($a >> 15)) * (1 | $a)) & 0xFFFFFFFF;
            $t = (($t + (($t ^ ($t >> 7)) * (61 | $t))) ^ $t) & 0xFFFFFFFF;
            return (($t ^ ($t >> 14)) & 0x7FFFFFFF) / 0x7FFFFFFF;
        };
    }

    private function mmss(?int $ms): ?string
    {
        if ($ms === null) return null;
        $sec = (int) round($ms / 1000);
        $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
        $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
        return $mm . ':' . $ss;
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $myDurationMs): array
    {
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends'], true)) $scope = 'global';

        $lbQuery = DailyGameRun::query()
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->whereNotNull('duration_ms');

        if ($scope === 'friends') {
            if (method_exists($user, 'friends')) {
                $friendIds = $user->friends()->pluck('users.id')->all();
                $ids = array_values(array_unique(array_merge([$user->id], $friendIds)));
                $lbQuery->whereIn('user_id', $ids);
            } else {
                $lbQuery->where('user_id', $user->id);
            }
        }

        $topTimes = (clone $lbQuery)
            ->with(['user:id,name,profile_picture,plan,level,xp'])
            ->orderBy('duration_ms')
            ->orderBy('finished_at')
            ->limit(10)
            ->get();

        $myRank = null;
        if ($myDurationMs !== null) {
            $myRank = (clone $lbQuery)
                    ->where('duration_ms', '<', $myDurationMs)
                    ->count() + 1;
        }

        $rows = $topTimes->map(function ($r) {
            $u = $r->user;
            return [
                'duration_ms' => (int) $r->duration_ms,
                'time' => $this->mmss((int) $r->duration_ms),
                'moves' => (int) $r->attempts,
                'user' => [
                    'id' => (int) $u->id,
                    'name' => (string) $u->name,
                    'level' => (int) ($u->level ?? 1),
                    'plan' => (string) ($u->plan ?? ''),
                    'profile_picture_url' => !empty($u->profile_picture) ? asset('storage/' . $u->profile_picture) : null,
                ],
            ];
        })->values()->all();

        return [
            'scope' => $scope,
            'rows' => $rows,
            'my_rank' => $myRank,
        ];
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();

        if ($user->plan !== 'pro') {
            $done = (int) $user->daily_challenges_done;
            $existing = DailyGameRun::where('user_id', $user->id)
                ->where('game_key', self::GAME_KEY)
                ->where('puzzle_date', $today->toDateString())
                ->exists();
            if (!$existing && $done >= 5) {
                return redirect()->route('dashboard')->with('limit_reached', true);
            }
        }

        $puzzle = $this->dailyPuzzle($today);

        $run = DailyGameRun::firstOrCreate(
            [
                'user_id' => $user->id,
                'game_key' => self::GAME_KEY,
                'puzzle_date' => $today->toDateString(),
            ],
            [
                'started_at' => now(),
                'state' => null,
                'attempts' => 0,
                'solved' => false,
            ]
        );

        $state = $run->state ?: ['started_ms' => now()->getTimestampMs()];

        if (!isset($state['started_ms'])) {
            $state['started_ms'] = $run->started_at?->getTimestampMs() ?? now()->getTimestampMs();
        }

        if (!$run->state) {
            $run->state = $state;
            $run->save();
        }

        $tabs = [
            ['key' => 'global', 'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden', 'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.color-sort', [
            'user' => $user,
            'puzzle' => $puzzle,
            'run' => $run,
            'state' => $state,
            'scope' => $lb['scope'],
            'tabs' => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank' => $lb['my_rank'],
            'streak' => $streak,
        ]);
    }

    public function solve(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        if ($run->solved) {
            if ($this->wantsJson($request)) {
                $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
                $streak = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);

                return response()->json([
                    'ok' => true,
                    'solved' => true,
                    'final_time' => $this->mmss($run->duration_ms),
                    'moves' => (int) $run->attempts,
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $moves = max(1, (int) $request->input('moves', 0));

        $run->solved = true;
        $run->finished_at = now();
        $run->attempts = $moves;

        $state = $run->state ?: ['started_ms' => now()->getTimestampMs()];
        $nowMs = now()->getTimestampMs();
        $startedMs = (int) ($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? $nowMs));
        $run->duration_ms = max(0, $nowMs - $startedMs);

        // Reward
        $todayStr = now()->toDateString();
        if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
            $user->daily_challenges_done = 0;
            $user->daily_challenges_date = $todayStr;
        }

        $limit = $user->plan === 'pro' ? null : 5;
        $canReward = is_null($limit) || ((int) $user->daily_challenges_done < (int) $limit);

        if ($canReward) {
            $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
            $user->addXp(100);
        } else {
            $user->save();
        }

        $run->state = $state;
        $run->save();

        app(\App\Services\DailyGameStreakService::class)
            ->recordSolved($user, self::GAME_KEY, $today);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        if ($this->wantsJson($request)) {
            $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);

            return response()->json([
                'ok' => true,
                'solved' => true,
                'final_time' => $this->mmss($run->duration_ms),
                'moves' => $moves,
                'leaderboard' => $lb,
                'streak' => $streak,
            ]);
        }

        return back();
    }
}
