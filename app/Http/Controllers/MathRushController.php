<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MathRushController extends Controller
{
    private const GAME_KEY = 'math-rush';
    private const ROUNDS = 15;

    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);
        $rng = $this->seededRng($seed);

        $ops = ['+', '-', '×', '÷'];
        $rounds = [];

        for ($i = 0; $i < self::ROUNDS; $i++) {
            // Difficulty scales with round number
            $tier = (int) floor($i / 5); // 0=easy, 1=medium, 2=hard

            switch ($tier) {
                case 0: // Easy: simple add/subtract
                    $op = $rng() < 0.5 ? '+' : '-';
                    $a = (int) (2 + floor($rng() * 18)); // 2-19
                    $b = (int) (2 + floor($rng() * 18)); // 2-19
                    if ($op === '-' && $b > $a) [$a, $b] = [$b, $a];
                    break;
                case 1: // Medium: bigger numbers + multiply
                    $opRoll = $rng();
                    if ($opRoll < 0.35) {
                        $op = '+';
                        $a = (int) (10 + floor($rng() * 90)); // 10-99
                        $b = (int) (10 + floor($rng() * 90));
                    } elseif ($opRoll < 0.7) {
                        $op = '-';
                        $a = (int) (20 + floor($rng() * 80));
                        $b = (int) (5 + floor($rng() * 45));
                        if ($b > $a) [$a, $b] = [$b, $a];
                    } else {
                        $op = '×';
                        $a = (int) (2 + floor($rng() * 11)); // 2-12
                        $b = (int) (2 + floor($rng() * 11));
                    }
                    break;
                default: // Hard: multiply + divide + bigger add/sub
                    $opRoll = $rng();
                    if ($opRoll < 0.25) {
                        $op = '+';
                        $a = (int) (50 + floor($rng() * 150)); // 50-199
                        $b = (int) (50 + floor($rng() * 150));
                    } elseif ($opRoll < 0.5) {
                        $op = '-';
                        $a = (int) (50 + floor($rng() * 150));
                        $b = (int) (10 + floor($rng() * 90));
                        if ($b > $a) [$a, $b] = [$b, $a];
                    } elseif ($opRoll < 0.75) {
                        $op = '×';
                        $a = (int) (3 + floor($rng() * 13)); // 3-15
                        $b = (int) (3 + floor($rng() * 13));
                    } else {
                        $op = '÷';
                        $b = (int) (2 + floor($rng() * 11)); // 2-12
                        $answer = (int) (2 + floor($rng() * 15)); // 2-16
                        $a = $b * $answer; // Ensure clean division
                    }
                    break;
            }

            // Calculate correct answer
            $answer = match ($op) {
                '+' => $a + $b,
                '-' => $a - $b,
                '×' => $a * $b,
                '÷' => (int) ($a / $b),
            };

            // Generate 3 wrong options
            $options = [$answer];
            $safetyCounter = 0;
            while (count($options) < 4 && $safetyCounter < 50) {
                $safetyCounter++;
                $offset = (int) (1 + floor($rng() * max(5, abs($answer) * 0.3)));
                $wrong = $rng() < 0.5 ? $answer + $offset : $answer - $offset;
                if ($wrong !== $answer && !in_array($wrong, $options, true)) {
                    $options[] = $wrong;
                }
            }

            // Fill remaining if needed
            while (count($options) < 4) {
                $options[] = $answer + count($options);
            }

            // Shuffle options deterministically
            for ($j = count($options) - 1; $j > 0; $j--) {
                $k = (int) floor($rng() * ($j + 1));
                [$options[$j], $options[$k]] = [$options[$k], $options[$j]];
            }

            $correctIdx = array_search($answer, $options, true);

            $rounds[] = [
                'a' => $a,
                'b' => $b,
                'op' => $op,
                'answer' => $answer,
                'options' => array_values($options),
                'correctIdx' => $correctIdx,
                'tier' => $tier,
            ];
        }

        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'totalRounds' => self::ROUNDS,
            'rounds' => $rounds,
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

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $durationMs): array
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
        if ($durationMs !== null) {
            $myRank = (clone $lbQuery)
                    ->where('duration_ms', '<', $durationMs)
                    ->count() + 1;
        }

        $fmtMs = function ($ms) {
            if ($ms === null) return '--:--';
            $sec = (int) round($ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        $rows = $topTimes->map(function ($r) use ($fmtMs) {
            $u = $r->user;
            $state = $r->state ?? [];
            return [
                'duration_ms' => (int) $r->duration_ms,
                'time' => $fmtMs((int) $r->duration_ms),
                'score' => (int) ($state['score'] ?? 0),
                'mistakes' => (int) ($state['mistakes'] ?? 0),
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

        $state = $run->state ?: [];

        $tabs = [
            ['key' => 'global', 'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden', 'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.math-rush', [
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

                $state = $run->state ?? [];
                $fmtMs = function ($ms) {
                    $sec = (int) round($ms / 1000);
                    $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
                    $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
                    return $mm . ':' . $ss;
                };

                return response()->json([
                    'ok' => true,
                    'solved' => true,
                    'final_time' => $fmtMs((int) $run->duration_ms),
                    'score' => (int) ($state['score'] ?? 0),
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $durationMs = max(1, (int) $request->input('duration_ms', 0));
        $score = max(0, (int) $request->input('score', 0));
        $mistakes = max(0, (int) $request->input('mistakes', 0));
        $answers = $request->input('answers', []);

        $fmtMs = function ($ms) {
            $sec = (int) round($ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        $run->solved = true;
        $run->finished_at = now();
        $run->duration_ms = $durationMs;
        $run->attempts = self::ROUNDS + $mistakes;
        $run->state = array_merge($run->state ?? [], [
            'score' => $score,
            'mistakes' => $mistakes,
            'answers' => $answers,
        ]);
        $run->save();

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
            $user->addXp(25);
        } else {
            $user->save();
        }

        app(\App\Services\DailyGameStreakService::class)
            ->recordSolved($user, self::GAME_KEY, $today);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        if ($this->wantsJson($request)) {
            $lb = $this->leaderboardPayload($request, $today, $user, $durationMs);

            return response()->json([
                'ok' => true,
                'solved' => true,
                'final_time' => $fmtMs($durationMs),
                'score' => $score,
                'leaderboard' => $lb,
                'streak' => $streak,
            ]);
        }

        return back();
    }
}
