<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MazeRunnerController extends Controller
{
    private const GAME_KEY = 'maze-runner';
    private const GRID_SIZE = 10; // 10x10 maze

    /**
     * Generate a maze using recursive backtracker with seeded RNG.
     * Each cell has walls: top, right, bottom, left (booleans).
     */
    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);
        $rng = $this->seededRng($seed);
        $size = self::GRID_SIZE;

        // Initialize grid — each cell has 4 walls
        $grid = [];
        for ($r = 0; $r < $size; $r++) {
            for ($c = 0; $c < $size; $c++) {
                $grid[$r][$c] = [
                    'top' => true,
                    'right' => true,
                    'bottom' => true,
                    'left' => true,
                ];
            }
        }

        // Recursive backtracker (iterative with stack)
        $visited = array_fill(0, $size, array_fill(0, $size, false));
        $stack = [[0, 0]];
        $visited[0][0] = true;

        $directions = [
            [0, -1, 'top', 'bottom'],    // up
            [0, 1, 'bottom', 'top'],     // down
            [-1, 0, 'left', 'right'],    // left
            [1, 0, 'right', 'left'],     // right
        ];

        while (!empty($stack)) {
            [$cx, $cy] = end($stack);

            // Find unvisited neighbors
            $neighbors = [];
            foreach ($directions as $dir) {
                $nx = $cx + $dir[0];
                $ny = $cy + $dir[1];
                if ($nx >= 0 && $nx < $size && $ny >= 0 && $ny < $size && !$visited[$ny][$nx]) {
                    $neighbors[] = [$nx, $ny, $dir[2], $dir[3]];
                }
            }

            if (empty($neighbors)) {
                array_pop($stack);
                continue;
            }

            // Pick random neighbor
            $idx = (int) floor($rng() * count($neighbors));
            [$nx, $ny, $wallCurrent, $wallNeighbor] = $neighbors[$idx];

            // Remove walls between current and neighbor
            $grid[$cy][$cx][$wallCurrent] = false;
            $grid[$ny][$nx][$wallNeighbor] = false;

            $visited[$ny][$nx] = true;
            $stack[] = [$nx, $ny];
        }

        // Start = top-left (0,0), End = bottom-right (size-1, size-1)
        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'size' => $size,
            'grid' => $grid,
            'start' => [0, 0],
            'end' => [$size - 1, $size - 1],
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

        return view('games.maze-runner', [
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
            $user->addXp(50);
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
