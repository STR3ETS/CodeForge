<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SudokuController extends Controller
{
    private const GAME_KEY     = 'sudoku';
    private const HIDDEN_CELLS = 8;   // hide 8 of 16 → 8 given cells

    // ─── RNG (same mulberry32 used across the app) ─────────────────────────

    private function rng(int $seed): \Closure
    {
        $a = $seed & 0xFFFFFFFF;
        return function () use (&$a): float {
            $a  = ($a + 0x6D2B79F5) & 0xFFFFFFFF;
            $t  = $a;
            $t  = ($t ^ ($t >> 15)) & 0xFFFFFFFF;
            $t  = (int)(($t * (1 | $t)) & 0xFFFFFFFF);
            $t2 = ($t ^ ($t >> 7)) & 0xFFFFFFFF;
            $t2 = (int)(($t2 * (61 | $t)) & 0xFFFFFFFF);
            $t  = ($t + $t2) & 0xFFFFFFFF;
            $t  = ($t ^ ($t >> 14)) & 0xFFFFFFFF;
            return $t / 4294967296;
        };
    }

    private function shuffleDet(array $items, \Closure $rand): array
    {
        $n = count($items);
        for ($i = $n - 1; $i > 0; $i--) {
            $j       = (int) floor($rand() * ($i + 1));
            $tmp     = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $tmp;
        }
        return $items;
    }

    // ─── Puzzle generation ─────────────────────────────────────────────────

    /**
     * Generate a valid complete 4×4 sudoku solution.
     * Applies symmetry-preserving transforms to a known base grid.
     * Returns a flat 16-element array (row-major order).
     */
    private function generateSolution(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|sol|' . $date->toDateString());
        $rand = $this->rng($seed);

        // Known valid 4×4 base (each row/col/2×2 box contains 1-4)
        $g = [
            [1, 2, 3, 4],
            [3, 4, 1, 2],
            [2, 1, 4, 3],
            [4, 3, 2, 1],
        ];

        // Swap rows within top band (rows 0 ↔ 1)
        if ($rand() > 0.5) { [$g[0], $g[1]] = [$g[1], $g[0]]; }
        // Swap rows within bottom band (rows 2 ↔ 3)
        if ($rand() > 0.5) { [$g[2], $g[3]] = [$g[3], $g[2]]; }
        // Swap cols within left stack (cols 0 ↔ 1)
        if ($rand() > 0.5) { foreach ($g as &$row) { [$row[0], $row[1]] = [$row[1], $row[0]]; } unset($row); }
        // Swap cols within right stack (cols 2 ↔ 3)
        if ($rand() > 0.5) { foreach ($g as &$row) { [$row[2], $row[3]] = [$row[3], $row[2]]; } unset($row); }
        // Swap top band ↔ bottom band
        if ($rand() > 0.5) { [$g[0], $g[2]] = [$g[2], $g[0]]; [$g[1], $g[3]] = [$g[3], $g[1]]; }
        // Swap left stack ↔ right stack
        if ($rand() > 0.5) {
            foreach ($g as &$row) {
                [$row[0], $row[2]] = [$row[2], $row[0]];
                [$row[1], $row[3]] = [$row[3], $row[1]];
            }
            unset($row);
        }
        // Relabel digits (permute 1-4)
        $labels = $this->shuffleDet([1, 2, 3, 4], $rand);
        $map    = array_combine([1, 2, 3, 4], $labels);
        foreach ($g as &$row) {
            foreach ($row as &$cell) { $cell = $map[$cell]; }
        }
        unset($row, $cell);

        // Flatten to 1D
        return array_merge(...$g);
    }

    /**
     * Returns array of 8 cell indices that should be hidden (the rest are given).
     */
    private function generateHiddenIndices(Carbon $date): array
    {
        $seed    = crc32(self::GAME_KEY . '|mask|' . $date->toDateString());
        $rand    = $this->rng($seed);
        $cells   = $this->shuffleDet(range(0, 15), $rand);
        return array_slice($cells, 0, self::HIDDEN_CELLS);
    }

    /**
     * Returns the puzzle data: puzzle number, given cells, hidden indices.
     */
    private function dailyPuzzle(Carbon $date): array
    {
        $solution = $this->generateSolution($date);
        $hidden   = $this->generateHiddenIndices($date);
        $seed     = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number   = 100 + (abs($seed) % 900);

        // given: [idx => value] for non-hidden cells
        $given = [];
        for ($i = 0; $i < 16; $i++) {
            if (!in_array($i, $hidden, true)) {
                $given[$i] = $solution[$i];
            }
        }

        return [
            'number' => $number,
            'date'   => $date->toDateString(),
            'given'  => $given,          // idx => value
            'hidden' => $hidden,         // array of hidden indices
        ];
    }

    /**
     * Check if player grid matches solution. Returns array of wrong cell indices.
     */
    private function getWrongCells(Carbon $date, array $flat16): array
    {
        $solution = $this->generateSolution($date);
        $hidden   = $this->generateHiddenIndices($date);
        $wrong    = [];
        foreach ($hidden as $idx) {
            if ((int)($flat16[$idx] ?? 0) !== (int)$solution[$idx]) {
                $wrong[] = $idx;
            }
        }
        return $wrong;
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function mmss(?int $ms): ?string
    {
        if ($ms === null) return null;
        $sec = (int) round($ms / 1000);
        return str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT)
            . ':' . str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
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
            $myRank = (clone $lbQuery)->where('duration_ms', '<', $myDurationMs)->count() + 1;
        }

        $rows = $topTimes->map(function ($r) {
            $u = $r->user;
            return [
                'duration_ms' => (int) $r->duration_ms,
                'time'        => $this->mmss((int) $r->duration_ms),
                'user'        => [
                    'id'                  => (int) $u->id,
                    'name'                => (string) $u->name,
                    'level'               => (int) ($u->level ?? 1),
                    'plan'                => (string) ($u->plan ?? ''),
                    'profile_picture_url' => !empty($u->profile_picture)
                        ? asset('storage/' . $u->profile_picture) : null,
                ],
            ];
        })->values()->all();

        return ['scope' => $scope, 'rows' => $rows, 'my_rank' => $myRank];
    }

    // ─── Controllers ───────────────────────────────────────────────────────

    public function show(Request $request)
    {
        $user  = $request->user();
        $today = now()->startOfDay();
        $puzzle = $this->dailyPuzzle($today);

        $run = DailyGameRun::firstOrCreate(
            [
                'user_id'     => $user->id,
                'game_key'    => self::GAME_KEY,
                'puzzle_date' => $today->toDateString(),
            ],
            [
                'started_at' => now(),
                'state'      => null,
                'attempts'   => 0,
                'solved'     => false,
            ]
        );

        $state = $run->state ?: [
            'started_ms' => now()->getTimestampMs(),
        ];
        if (!isset($state['started_ms'])) {
            $state['started_ms'] = $run->started_at?->getTimestampMs() ?? now()->getTimestampMs();
        }
        $run->state = $state;
        $run->save();

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lb     = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);
        $streak = app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today);

        $isFailed = (!$run->solved && !empty($run->finished_at));

        return view('games.sudoku', [
            'user'     => $user,
            'puzzle'   => $puzzle,
            'run'      => $run,
            'state'    => $state,
            'isFailed' => $isFailed,
            'scope'    => $lb['scope'],
            'tabs'     => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank'   => $lb['my_rank'],
            'streak'   => $streak,
        ]);
    }

    public function check(Request $request)
    {
        $user  = $request->user();
        $today = now()->startOfDay();

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        // Already finished
        if ($run->solved || (!empty($run->finished_at) && !$run->solved)) {
            if ($this->wantsJson($request)) {
                $lb     = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);
                $streak = app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today);
                return response()->json([
                    'ok'         => true,
                    'solved'     => (bool) $run->solved,
                    'failed'     => !$run->solved,
                    'final_time' => $this->mmss($run->duration_ms),
                    'leaderboard' => $lb,
                    'streak'     => $streak,
                ]);
            }
            return back();
        }

        $state               = $run->state ?: ['started_ms' => now()->getTimestampMs()];
        $state['started_ms'] = (int) ($state['started_ms'] ?? now()->getTimestampMs());

        // Receive flat 16-value array from client
        $raw  = $request->input('grid', []);
        $flat = [];
        for ($i = 0; $i < 16; $i++) {
            $flat[$i] = (int) ($raw[$i] ?? 0);
        }

        $wrongCells = $this->getWrongCells($today, $flat);
        $correct    = empty($wrongCells);

        $run->attempts = (int) $run->attempts + 1;

        $finished = false;
        $solved   = false;
        $failed   = false;

        if ($correct) {
            $finished = true;
            $solved   = true;

            $run->solved      = true;
            $run->finished_at = now();
            $nowMs            = now()->getTimestampMs();
            $run->duration_ms = max(0, $nowMs - $state['started_ms']);

            // XP reward
            $todayStr = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr;
            }
            $limit     = $user->plan === 'pro' ? null : 5;
            $canReward = is_null($limit) || ((int) $user->daily_challenges_done < (int) $limit);
            if ($canReward) {
                $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
                $user->addXp(150);
            } else {
                $user->save();
            }

            app(\App\Services\DailyGameStreakService::class)->recordSolved($user, self::GAME_KEY, $today);
        } else {
            // One chance: immediately failed
            $finished         = true;
            $failed           = true;
            $run->solved      = false;
            $run->finished_at = now();
            $run->duration_ms = null;

            app(\App\Services\DailyGameStreakService::class)->recordSolved($user, self::GAME_KEY, $today);
        }

        $run->state = $state;
        $run->save();

        $payload = [
            'ok'          => true,
            'correct'     => $correct,
            'wrong_cells' => $wrongCells,
            'finished'    => $finished,
            'solved'      => $solved,
            'failed'      => $failed,
            'final_time'  => $this->mmss($run->duration_ms),
        ];

        if ($solved && $run->duration_ms !== null) {
            $payload['leaderboard'] = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
        }

        $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        if ($this->wantsJson($request)) {
            return response()->json($payload);
        }

        return back();
    }
}
