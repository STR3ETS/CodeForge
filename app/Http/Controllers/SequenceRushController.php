<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SequenceRushController extends Controller
{
    private const GAME_KEY = 'sequence-rush';

    private const TOTAL_QUESTIONS = 3;
    private const MAX_WRONG = 2;
    private const PENALTY_MS_PER_WRONG = 3000;

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

    /**
     * Deterministic RNG (mulberry32-ish) for PHP.
     */
    private function rng(int $seed): \Closure
    {
        $a = $seed & 0xFFFFFFFF;

        return function () use (&$a): float {
            $a = ($a + 0x6D2B79F5) & 0xFFFFFFFF;

            $t = $a;
            $t = ($t ^ ($t >> 15)) & 0xFFFFFFFF;
            $t = (int) (($t * (1 | $t)) & 0xFFFFFFFF);

            $t2 = ($t ^ ($t >> 7)) & 0xFFFFFFFF;
            $t2 = (int) (($t2 * (61 | $t)) & 0xFFFFFFFF);

            $t = ($t + $t2) & 0xFFFFFFFF;
            $t = ($t ^ ($t >> 14)) & 0xFFFFFFFF;

            return ($t / 4294967296); // 0..1
        };
    }

    private function rint(\Closure $rand, int $min, int $max): int
    {
        if ($max <= $min) return $min;
        $v = $rand();
        return $min + (int) floor($v * (($max - $min) + 1));
    }

    private function shuffleDeterministic(array $items, \Closure $rand): array
    {
        $n = count($items);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = (int) floor($rand() * ($i + 1));
            $tmp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $tmp;
        }
        return $items;
    }

    private function makeQuestion(Carbon $date, int $index): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString() . '|Q|' . $index);
        $rand = $this->rng($seed);

        $types = [
            'arith',      // arithmetic progression
            'geom',       // geometric progression
            'squares',    // n^2
            'tri',        // triangular numbers
            'fib',        // fibonacci-like
            'alt',        // alternating +a -b
            'mul_add',    // x = x*2 + k
            'primes',     // prime sequence
        ];

        $type = $types[$seed % count($types)];

        $terms = [];
        $answer = null;
        $hint = null;

        if ($type === 'arith') {
            $start = $this->rint($rand, 1, 25);
            $step  = $this->rint($rand, 1, 9);
            for ($i = 0; $i < 5; $i++) $terms[] = $start + ($i * $step);
            $answer = $terms[3];
            $hint = "+{$step}";
        }

        if ($type === 'geom') {
            $start = $this->rint($rand, 1, 8);
            $ratio = $this->rint($rand, 2, 4);
            $terms[] = $start;
            for ($i = 1; $i < 5; $i++) $terms[] = $terms[$i - 1] * $ratio;
            $answer = $terms[3];
            $hint = "×{$ratio}";
        }

        if ($type === 'squares') {
            $n = $this->rint($rand, 1, 10);
            for ($i = 0; $i < 5; $i++) $terms[] = ($n + $i) * ($n + $i);
            $answer = $terms[3];
            $hint = "n²";
        }

        if ($type === 'tri') {
            $n = $this->rint($rand, 1, 12);
            $tri = fn ($k) => (int)(($k * ($k + 1)) / 2);
            for ($i = 0; $i < 5; $i++) $terms[] = $tri($n + $i);
            $answer = $terms[3];
            $hint = "triangular";
        }

        if ($type === 'fib') {
            $a = $this->rint($rand, 1, 9);
            $b = $this->rint($rand, 1, 9);
            $terms = [$a, $b];
            for ($i = 2; $i < 5; $i++) $terms[] = $terms[$i - 1] + $terms[$i - 2];
            $answer = $terms[3];
            $hint = "prev + prev";
        }

        if ($type === 'alt') {
            $start = $this->rint($rand, 5, 35);
            $a = $this->rint($rand, 2, 9);
            $b = $this->rint($rand, 1, 7);

            $t1 = $start;
            $t2 = $t1 + $a;
            $t3 = $t2 - $b;
            $t4 = $t3 + $a;
            $t5 = $t4 - $b;

            $terms = [$t1, $t2, $t3, $t4, $t5];
            $answer = $terms[3];
            $hint = "+{$a}, -{$b}";
        }

        if ($type === 'mul_add') {
            $start = $this->rint($rand, 1, 10);
            $k = $this->rint($rand, 1, 9);
            $terms[] = $start;
            for ($i = 1; $i < 5; $i++) $terms[] = ($terms[$i - 1] * 2) + $k;
            $answer = $terms[3];
            $hint = "×2 + {$k}";
        }

        if ($type === 'primes') {
            $primes = [2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97];
            $startIdx = $this->rint($rand, 0, max(0, count($primes) - 6));
            for ($i = 0; $i < 5; $i++) $terms[] = $primes[$startIdx + $i];
            $answer = $terms[3];
            $hint = "primes";
        }

        // Hide 4th term
        $display = [
            (string)$terms[0],
            (string)$terms[1],
            (string)$terms[2],
            '?',
            (string)$terms[4],
        ];

        $prompt = implode(', ', $display);

        // Make options
        $options = [$answer];

        $stepGuess = abs($terms[1] - $terms[0]);
        $candidates = [
            $answer + $stepGuess,
            $answer - $stepGuess,
            $answer + 1,
            $answer - 1,
            $answer + 2,
            $answer - 2,
        ];

        foreach ($candidates as $c) {
            if (count($options) >= 4) break;
            if ($c <= 0) continue;
            if (in_array($c, $options, true)) continue;
            $options[] = $c;
        }

        while (count($options) < 4) {
            $delta = $this->rint($rand, -12, 12);
            if ($delta === 0) continue;
            $c = $answer + $delta;
            if ($c <= 0) continue;
            if (in_array($c, $options, true)) continue;
            $options[] = $c;
        }

        $options = $this->shuffleDeterministic($options, $rand);

        return [
            'idx' => $index,
            'prompt' => $prompt,
            'options' => array_values($options),
            'answer' => (int)$answer,
            'hint' => (string)$hint,
        ];
    }

    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);

        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'total' => self::TOTAL_QUESTIONS,
            'max_wrong' => self::MAX_WRONG,
            'penalty_ms' => self::PENALTY_MS_PER_WRONG,
        ];
    }

    private function initialState(): array
    {
        return [
            'started_ms' => now()->getTimestampMs(),
            'current_idx' => 0,
            'wrong' => 0,
            'answered' => 0,
        ];
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

        $puzzleMeta = $this->dailyPuzzle($today);

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

        $state = $run->state ?: $this->initialState();

        if (!isset($state['started_ms'])) {
            $state['started_ms'] = $run->started_at?->getTimestampMs() ?? now()->getTimestampMs();
        }
        $state['current_idx'] = (int)($state['current_idx'] ?? 0);
        $state['wrong'] = (int)($state['wrong'] ?? 0);
        $state['answered'] = (int)($state['answered'] ?? 0);

        $run->state = $state;
        if (!$run->wasRecentlyCreated) {
            $run->save();
        } else {
            $run->save();
        }

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        $isFailed = (!$run->solved && !empty($run->finished_at));

        $currentIdx = min(self::TOTAL_QUESTIONS - 1, max(0, (int)($state['current_idx'] ?? 0)));
        $question = $this->makeQuestion($today, $currentIdx);

        return view('games.sequence', [
            'user' => $user,
            'puzzleMeta' => $puzzleMeta,
            'question' => $question,

            'run' => $run,
            'state' => $state,
            'isFailed' => $isFailed,

            'scope' => $lb['scope'],
            'tabs' => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank' => $lb['my_rank'],

            'streak' => $streak,
        ]);
    }

    public function answer(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        // already finished
        if ($run->solved || (!empty($run->finished_at) && !$run->solved)) {
            if ($this->wantsJson($request)) {
                $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);
                $streak = app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today);

                return response()->json([
                    'ok' => true,
                    'solved' => (bool)$run->solved,
                    'failed' => (bool)(!$run->solved && !empty($run->finished_at)),
                    'final_time' => $this->mmss($run->duration_ms),
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $state = $run->state ?: $this->initialState();
        $state['current_idx'] = (int)($state['current_idx'] ?? 0);
        $state['wrong'] = (int)($state['wrong'] ?? 0);
        $state['answered'] = (int)($state['answered'] ?? 0);
        $state['started_ms'] = (int)($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? now()->getTimestampMs()));

        $idx = (int) $request->input('idx', $state['current_idx']);
        $choice = (int) $request->input('choice', 0);

        // always sync to server's idx
        $idx = (int)$state['current_idx'];

        $q = $this->makeQuestion($today, $idx);
        $correct = ((int)$choice === (int)$q['answer']);

        $state['answered'] = (int)$state['answered'] + 1;
        $run->attempts = (int)$state['answered'];

        if (!$correct) {
            $state['wrong'] = (int)$state['wrong'] + 1;
        }

        $finished = false;
        $solved = false;
        $failed = false;

        // fail condition
        if ((int)$state['wrong'] >= self::MAX_WRONG) {
            $finished = true;
            $failed = true;

            $run->finished_at = now();
            $run->duration_ms = null;
            $run->solved = false;

            // Count as played game for free-user limit
            $todayStr2 = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr2) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr2;
            }
            $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
            $user->save();

            // ✅ keep global streak alive (same pattern as WordForge)
            app(\App\Services\DailyGameStreakService::class)
                ->recordSolved($user, self::GAME_KEY, $today);
        } else {
            // next question if correct OR wrong (we still continue)
            $state['current_idx'] = (int)$state['current_idx'] + 1;

            if ((int)$state['current_idx'] >= self::TOTAL_QUESTIONS) {
                $finished = true;
                $solved = true;

                $run->solved = true;
                $run->finished_at = now();

                $nowMs = now()->getTimestampMs();
                $raw = max(0, $nowMs - (int)$state['started_ms']);
                $penalty = ((int)$state['wrong']) * self::PENALTY_MS_PER_WRONG;

                // ✅ store final score time in duration_ms (raw + penalty)
                $run->duration_ms = $raw + $penalty;

                // reward logic (same as other games)
                $todayStr = now()->toDateString();
                if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                    $user->daily_challenges_done = 0;
                    $user->daily_challenges_date = $todayStr;
                }

                $limit = $user->plan === 'pro' ? null : 5;
                $canReward = is_null($limit) || ((int)$user->daily_challenges_done < (int)$limit);

                if ($canReward) {
                    $user->daily_challenges_done = (int)$user->daily_challenges_done + 1;
                    $user->addXp(50);
                } else {
                    $user->save();
                }

                app(\App\Services\DailyGameStreakService::class)
                    ->recordSolved($user, self::GAME_KEY, $today);
            }
        }

        $run->state = $state;
        $run->save();

        $payload = [
            'ok' => true,
            'correct' => $correct,
            'current_idx' => (int)($state['current_idx'] ?? 0),
            'wrong' => (int)($state['wrong'] ?? 0),
            'answered' => (int)($state['answered'] ?? 0),

            'finished' => $finished,
            'solved' => $solved,
            'failed' => $failed,

            'final_time' => $this->mmss($run->duration_ms),
        ];

        // next question
        if (!$finished) {
            $nextIdx = min(self::TOTAL_QUESTIONS - 1, max(0, (int)$state['current_idx']));
            $payload['question'] = $this->makeQuestion($today, $nextIdx);
        }

        // streak UI
        $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        // leaderboard only on solved
        if ($solved && $run->duration_ms !== null) {
            $payload['leaderboard'] = $this->leaderboardPayload($request, $today, $user, (int)$run->duration_ms);
        }

        if ($this->wantsJson($request)) {
            return response()->json($payload);
        }

        return back();
    }
}