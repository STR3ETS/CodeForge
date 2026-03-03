<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WordForgeController extends Controller
{
    private const GAME_KEY = 'word-forge';
    private const MAX_ATTEMPTS = 5;
    private const MIN_LEN = 3;
    private const MAX_LEN = 12;

    private function pool(): array
    {
        return [
            ['category' => 'Tech',   'word' => 'API'],
            ['category' => 'Tech',   'word' => 'CACHE'],
            ['category' => 'Tech',   'word' => 'FRAMEWORK'],
            ['category' => 'Tech',   'word' => 'APPLICATION'],

            ['category' => 'Cities', 'word' => 'ROME'],
            ['category' => 'Cities', 'word' => 'AMSTERDAM'],
            ['category' => 'Cities', 'word' => 'COPENHAGEN'],

            ['category' => 'Food',   'word' => 'TEA'],
            ['category' => 'Food',   'word' => 'SUSHI'],
            ['category' => 'Food',   'word' => 'CHOCOLATE'],

            ['category' => 'Nature', 'word' => 'OAK'],
            ['category' => 'Nature', 'word' => 'MOUNTAIN'],
        ];
    }

    private function dailyPuzzle(Carbon $date): array
    {
        $pool = array_values(array_filter($this->pool(), function ($e) {
            $w = strtoupper((string)($e['word'] ?? ''));
            $len = strlen($w);
            return $len >= self::MIN_LEN && $len <= self::MAX_LEN;
        }));

        $byCategory = [];
        foreach ($pool as $entry) {
            $cat = (string) $entry['category'];
            $byCategory[$cat] ??= [];
            $byCategory[$cat][] = strtoupper((string) $entry['word']);
        }

        $categories = array_values(array_keys($byCategory));
        sort($categories);

        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $catIndex = $seed % max(1, count($categories));
        $category = $categories[$catIndex];

        $words = $byCategory[$category];
        $seed2 = crc32('W|' . self::GAME_KEY . '|' . $date->toDateString());
        $wordIndex = $seed2 % max(1, count($words));
        $word = $words[$wordIndex];

        $number = 100 + ($seed % 900);

        return [
            'number'   => $number,
            'date'     => $date->toDateString(),
            'category' => $category,
            'word'     => $word,
            'length'   => strlen($word),
            'first'    => substr($word, 0, 1),
        ];
    }

    private function initialState(array $puzzle): array
    {
        $pattern = $puzzle['first'] . str_repeat('_', $puzzle['length'] - 1);

        return [
            'pattern'      => $pattern,
            'attempts'     => [],
            'max_attempts' => self::MAX_ATTEMPTS,
            'length'       => $puzzle['length'],
            'first'        => $puzzle['first'],
            'started_ms'   => now()->getTimestampMs(),
        ];
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function mmss(?int $ms): ?string
    {
        if ($ms === null) return null;

        $sec = (int) round($ms / 1000);
        $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
        $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);

        return $mm . ':' . $ss;
    }

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $myDurationMs): array
    {
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends'], true)) $scope = 'global';

        $tabs = [
            ['key' => 'global',  'label' => 'Worldwide', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Friends',   'icon' => 'fa-solid fa-user-group'],
        ];

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
                'time' => $this->mmss((int) $r->duration_ms),
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
            'tabs' => $tabs,
            'rows' => $rows,
            'my_rank' => $myRank,
        ];
    }

    public function show(Request $request)
    {
        $user = $request->user();
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

        $state = $run->state ?: $this->initialState($puzzle);

        if (!isset($state['started_ms'])) {
            $state['started_ms'] = $run->started_at?->getTimestampMs() ?? now()->getTimestampMs();
            $run->state = $state;
            $run->save();
        }

        if (!$run->state) {
            $run->state = $state;
            $run->attempts = 0;
            $run->save();
        }

        $attemptsUsed = count($state['attempts'] ?? []);
        $attemptsLeft = max(0, (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS) - $attemptsUsed);

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.word-forge', [
            'user'         => $user,
            'puzzle'       => $puzzle,
            'run'          => $run,
            'state'        => $state,
            'attemptsUsed' => $attemptsUsed,
            'attemptsLeft' => $attemptsLeft,

            'scope' => $lb['scope'],
            'tabs' => $lb['tabs'],
            'topTimes' => collect($lb['rows']),
            'myRank' => $lb['my_rank'],

            'streak' => $streak,
        ]);
    }

    public function guess(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $puzzle = $this->dailyPuzzle($today);

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        // Already solved/failed
        if ($run->solved || ($run->finished_at && !$run->solved)) {
            if ($this->wantsJson($request)) {
                $state = $run->state ?: $this->initialState($puzzle);
                $attemptsUsed = count($state['attempts'] ?? []);
                $attemptsLeft = max(0, self::MAX_ATTEMPTS - $attemptsUsed);
                $failed = (!$run->solved && $run->finished_at && $attemptsLeft <= 0);

                $payload = [
                    'ok' => true,
                    'pattern' => (string)($state['pattern'] ?? ''),
                    'attempts' => (array)($state['attempts'] ?? []),
                    'attemptsUsed' => $attemptsUsed,
                    'attemptsLeft' => $attemptsLeft,
                    'solved' => (bool)$run->solved,
                    'failed' => (bool)$failed,
                    'final_time' => $this->mmss($run->duration_ms),
                    'answer' => $run->solved ? $puzzle['word'] : ($failed ? $puzzle['word'] : null),
                ];

                // if solved already, include leaderboard too (no reload)
                if ($run->solved && $run->duration_ms !== null) {
                    $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
                    $payload['leaderboard'] = $lb;
                }

                $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);

                return response()->json($payload);
            }
            return back();
        }

        $state = $run->state ?: $this->initialState($puzzle);

        $attemptsUsed = count($state['attempts'] ?? []);
        if ($attemptsUsed >= (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS)) {
            $run->finished_at = now();
            $run->duration_ms = null;
            $run->solved = false;
            $run->save();

            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => true,
                    'pattern' => (string)($state['pattern'] ?? ''),
                    'attempts' => (array)($state['attempts'] ?? []),
                    'attemptsUsed' => $attemptsUsed,
                    'attemptsLeft' => 0,
                    'solved' => false,
                    'failed' => true,
                    'final_time' => null,
                    'answer' => $puzzle['word'],
                    'streak' => app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today),
                ]);
            }

            return back();
        }

        $guess = strtoupper(trim((string) $request->input('guess', '')));
        $guess = preg_replace('/\s+/', '', $guess);

        $fail422 = function (string $msg) use ($request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => ['guess' => [$msg]],
                ], 422);
            }
            return back()->withErrors(['guess' => $msg]);
        };

        if ($guess === '' || !preg_match('/^[A-Z]+$/', $guess)) {
            return $fail422('Use letters A–Z only.');
        }

        if (strlen($guess) !== (int)$puzzle['length']) {
            return $fail422('Your guess must be exactly ' . $puzzle['length'] . ' letters.');
        }

        if (substr($guess, 0, 1) !== $puzzle['first']) {
            return $fail422('Your guess must start with "' . $puzzle['first'] . '".');
        }

        $answer = $puzzle['word'];

        $mask = [];
        for ($i = 0; $i < $puzzle['length']; $i++) {
            $mask[$i] = ($guess[$i] === $answer[$i]) ? 1 : 0;
        }

        $pattern = str_split((string)($state['pattern'] ?? ($puzzle['first'] . str_repeat('_', $puzzle['length'] - 1))));
        for ($i = 0; $i < $puzzle['length']; $i++) {
            if ($mask[$i] === 1) $pattern[$i] = $answer[$i];
        }

        $state['pattern'] = implode('', $pattern);
        $state['attempts'][] = [
            'guess' => $guess,
            'mask'  => $mask,
        ];

        $attemptsUsed++;
        $justSolved = ($guess === $answer);

        $justFinishedToday = false;

        $run->state = $state;
        $run->attempts = $attemptsUsed;

        if ($justSolved) {
            $run->solved = true;
            $run->finished_at = now();

            $nowMs = now()->getTimestampMs();
            $startedMs = (int)($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? $nowMs));
            $run->duration_ms = max(0, $nowMs - $startedMs);

            // reward logic
            $todayStr = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr;
            }

            $limit = $user->plan === 'pro' ? null : 5;
            $canReward = is_null($limit) || ((int)$user->daily_challenges_done < (int)$limit);

            if ($canReward) {
                $user->daily_challenges_done = (int)$user->daily_challenges_done + 1;
                $user->addXp(150);
            } else {
                $user->save();
            }

            $justFinishedToday = true;
        } else {
            $max = (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS);
            if ($attemptsUsed >= $max) {
                $run->finished_at = now();
                $run->duration_ms = null;
                $run->solved = false;

                $justFinishedToday = true; // ✅ game klaar (failed)
            }
        }

        $run->save();

        // ✅ STAP 4.4: streak registreren zodra de game klaar is (solved OF failed)
        if ($justFinishedToday) {
            app(\App\Services\DailyGameStreakService::class)
                ->recordSolved($user, self::GAME_KEY, $today);
        }

        $attemptsLeft = max(0, self::MAX_ATTEMPTS - $attemptsUsed);
        $failed = (!$run->solved && $run->finished_at && $attemptsLeft <= 0);

        if ($this->wantsJson($request)) {
            $payload = [
                'ok' => true,
                'pattern' => (string)($state['pattern'] ?? ''),
                'attempts' => (array)($state['attempts'] ?? []),
                'attemptsUsed' => $attemptsUsed,
                'attemptsLeft' => $attemptsLeft,
                'solved' => (bool)$run->solved,
                'failed' => (bool)$failed,
                'final_time' => $this->mmss($run->duration_ms),
                'answer' => $run->solved ? $puzzle['word'] : ($failed ? $puzzle['word'] : null),
            ];

            // ✅ if solved, return live leaderboard too (no reload)
            if ($run->solved && $run->duration_ms !== null) {
                $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
                $payload['leaderboard'] = $lb;
            }

            // ✅ ADD: altijd meest recente streak meegeven (na eventuele recordSolved)
            $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
                ->uiPayload($user, self::GAME_KEY, $today);

            return response()->json($payload);
        }

        return back();
    }
}