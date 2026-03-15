<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BlockDropController extends Controller
{
    private const GAME_KEY = 'block-drop';

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function mmss(?int $ms): ?string
    {
        if ($ms === null) return null;
        $sec = (int) round($ms / 1000);
        $mm  = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
        $ss  = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
        return $mm . ':' . $ss;
    }

    private function dailyPuzzle(Carbon $date): array
    {
        $seed   = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + (abs($seed) % 900);

        return [
            'number' => $number,
            'date'   => $date->toDateString(),
            'seed'   => $seed,
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
                $ids       = array_values(array_unique(array_merge([$user->id], $friendIds)));
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
                    'profile_picture_url' => !empty($u->profile_picture) ? asset('storage/' . $u->profile_picture) : null,
                ],
            ];
        })->values()->all();

        return [
            'scope'   => $scope,
            'rows'    => $rows,
            'my_rank' => $myRank,
        ];
    }

    public function show(Request $request)
    {
        $user  = $request->user();
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

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        $isFailed = (!$run->solved && !empty($run->finished_at));

        $fmtMs = function ($ms) {
            if ($ms === null) return null;
            $sec = (int) round($ms / 1000);
            $mm  = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss  = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        return view('games.block-drop', [
            'user'       => $user,
            'puzzleMeta' => $puzzleMeta,

            'run'      => $run,
            'isFailed' => $isFailed,
            'finalTime' => $run->solved ? $fmtMs($run->duration_ms) : null,

            'scope'    => $lb['scope'],
            'tabs'     => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank'   => $lb['my_rank'],

            'streak' => $streak,
        ]);
    }

    public function finish(Request $request)
    {
        $user  = $request->user();
        $today = now()->startOfDay();

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        // Already finished — return current state (idempotent)
        if ($run->solved || (!empty($run->finished_at) && !$run->solved)) {
            $lb     = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);
            $streak = app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today);

            return response()->json([
                'ok'         => true,
                'solved'     => (bool) $run->solved,
                'failed'     => (bool) (!$run->solved && !empty($run->finished_at)),
                'final_time' => $this->mmss($run->duration_ms),
                'leaderboard' => $lb,
                'streak'     => $streak,
            ]);
        }

        $failed     = (bool) $request->input('failed', false);
        $durationMs = $failed ? null : max(0, (int) $request->input('duration_ms', 0));

        $run->finished_at = now();

        if ($failed) {
            $run->solved      = false;
            $run->duration_ms = null;

            // Count as played game for free-user limit
            $todayStr = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr;
            }
            $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
            $user->save();
        } else {
            $run->solved      = true;
            $run->duration_ms = $durationMs;

            // Reward XP
            $todayStr = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr;
            }

            $limit     = $user->plan === 'pro' ? null : 5;
            $canReward = is_null($limit) || ((int) $user->daily_challenges_done < (int) $limit);

            if ($canReward) {
                $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
                $user->addXp(100);
            } else {
                $user->save();
            }
        }

        $run->save();

        app(\App\Services\DailyGameStreakService::class)
            ->recordSolved($user, self::GAME_KEY, $today);

        $lb     = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);
        $streak = app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today);

        return response()->json([
            'ok'         => true,
            'solved'     => (bool) $run->solved,
            'failed'     => $failed,
            'final_time' => $this->mmss($run->duration_ms),
            'leaderboard' => $lb,
            'streak'     => $streak,
        ]);
    }
}
