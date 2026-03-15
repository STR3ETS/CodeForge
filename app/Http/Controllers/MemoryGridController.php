<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MemoryGridController extends Controller
{
    private const GAME_KEY = 'memory-grid';

    /**
     * Emoji pool — grouped by theme so pairs feel thematic.
     */
    private function emojiPool(): array
    {
        return [
            '🍎','🍊','🍋','🍇','🍓','🍒','🍑','🥝','🍌','🍉',
            '🌸','🌻','🌷','🌹','🌺','🍀','🌵','🌴','🎄','🌿',
            '🐶','🐱','🐼','🐨','🦊','🐸','🐧','🦋','🐙','🐬',
            '⚡','🔥','❄️','🌊','🌈','⭐','🌙','☀️','💎','🪐',
            '🎸','🎹','🥁','🎺','🎻','🎮','🎯','🎲','🏀','⚽',
            '🚀','✈️','🚗','🚂','⛵','🏰','🗽','🎡','🎢','🏔️',
        ];
    }

    /**
     * Build daily puzzle: deterministic grid based on date.
     * Difficulty scales with grid size: 4x3 (6 pairs), 4x4 (8 pairs), 5x4 (10 pairs).
     */
    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);

        // Grid: 4 cols x 4 rows = 16 cards = 8 pairs
        $cols = 4;
        $rows = 4;
        $pairCount = ($cols * $rows) / 2;

        // Pick emojis deterministically
        $pool = $this->emojiPool();
        $rng = $this->seededRng($seed);
        $shuffledPool = $pool;
        // Fisher-Yates with seeded RNG
        for ($i = count($shuffledPool) - 1; $i > 0; $i--) {
            $j = (int) floor($rng() * ($i + 1));
            [$shuffledPool[$i], $shuffledPool[$j]] = [$shuffledPool[$j], $shuffledPool[$i]];
        }
        $selected = array_slice($shuffledPool, 0, $pairCount);

        // Create pairs and shuffle positions
        $cards = [];
        foreach ($selected as $idx => $emoji) {
            $cards[] = ['id' => $idx * 2, 'emoji' => $emoji, 'pairId' => $idx];
            $cards[] = ['id' => $idx * 2 + 1, 'emoji' => $emoji, 'pairId' => $idx];
        }

        // Shuffle card positions
        $seed2 = crc32('SHUFFLE|' . self::GAME_KEY . '|' . $date->toDateString());
        $rng2 = $this->seededRng($seed2);
        for ($i = count($cards) - 1; $i > 0; $i--) {
            $j = (int) floor($rng2() * ($i + 1));
            [$cards[$i], $cards[$j]] = [$cards[$j], $cards[$i]];
        }

        // Re-index IDs based on position
        foreach ($cards as $pos => &$card) {
            $card['pos'] = $pos;
        }
        unset($card);

        // Memorize time in seconds
        $memorizeTime = 4;

        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'cols' => $cols,
            'rows' => $rows,
            'pairCount' => $pairCount,
            'cards' => $cards,
            'memorizeTime' => $memorizeTime,
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

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $myAttempts): array
    {
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends'], true)) $scope = 'global';

        $lbQuery = DailyGameRun::query()
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->where('attempts', '>', 0);

        if ($scope === 'friends') {
            if (method_exists($user, 'friends')) {
                $friendIds = $user->friends()->pluck('users.id')->all();
                $ids = array_values(array_unique(array_merge([$user->id], $friendIds)));
                $lbQuery->whereIn('user_id', $ids);
            } else {
                $lbQuery->where('user_id', $user->id);
            }
        }

        $topScores = (clone $lbQuery)
            ->with(['user:id,name,profile_picture,plan,level,xp'])
            ->orderBy('attempts')
            ->orderBy('duration_ms')
            ->limit(10)
            ->get();

        $myRank = null;
        if ($myAttempts !== null) {
            $myRank = (clone $lbQuery)
                    ->where('attempts', '<', $myAttempts)
                    ->count() + 1;
        }

        $rows = $topScores->map(function ($r) {
            $u = $r->user;
            return [
                'duration_ms' => (int) $r->duration_ms,
                'time' => $this->mmss((int) $r->duration_ms),
                'attempts' => (int) $r->attempts,
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

        // Free-user daily limit
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

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->attempts : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.memory-grid', [
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
                $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->attempts);
                $streak = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);

                return response()->json([
                    'ok' => true,
                    'solved' => true,
                    'final_time' => $this->mmss($run->duration_ms),
                    'attempts' => (int) $run->attempts,
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $attempts = max(1, (int) $request->input('attempts', 0));

        $run->solved = true;
        $run->finished_at = now();
        $run->attempts = $attempts;

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
            $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->attempts);

            return response()->json([
                'ok' => true,
                'solved' => true,
                'final_time' => $this->mmss($run->duration_ms),
                'attempts' => $attempts,
                'leaderboard' => $lb,
                'streak' => $streak,
            ]);
        }

        return back();
    }
}
