<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FindTheEmojiController extends Controller
{
    private const GAME_KEY = 'find-the-emoji';

private function pool(): array
{
    return [
        // =========================
        // Faces (subtiel - zelfde kleur / zelfde stijl)
        // =========================
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😃', 'target' => '😄'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😁', 'target' => '😆'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '🙂', 'target' => '😊'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😉', 'target' => '😌'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😗', 'target' => '😙'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😚', 'target' => '😙'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😛', 'target' => '😜'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😜', 'target' => '🤪'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '🥹', 'target' => '🥲'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😐', 'target' => '😑'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😶', 'target' => '🫥'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😮', 'target' => '😯'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😦', 'target' => '😧'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😕', 'target' => '🫤'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😠', 'target' => '😡'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😳', 'target' => '🫣'],
        ['label' => 'Face', 'render_type' => 'emoji', 'base' => '😴', 'target' => '😪'],

        // =========================
        // Hearts (zelfde shape, alleen kleur/variant)
        // =========================
        ['label' => 'Hearts', 'render_type' => 'emoji', 'base' => '💓', 'target' => '💗'],
        ['label' => 'Hearts', 'render_type' => 'emoji', 'base' => '💕', 'target' => '💞'],

        // =========================
        // Stars / sparkle / moon (zelfde “sky” family)
        // =========================
        ['label' => 'Sky', 'render_type' => 'emoji', 'base' => '⭐', 'target' => '🌟'],
        ['label' => 'Sky', 'render_type' => 'emoji', 'base' => '🌙', 'target' => '🌛'],
        ['label' => 'Sky', 'render_type' => 'emoji', 'base' => '🌕', 'target' => '🌖'],
        ['label' => 'Sky', 'render_type' => 'emoji', 'base' => '🌑', 'target' => '🌘'],

        // =========================
        // Plants (groen, zelfde vibe)
        // =========================
        ['label' => 'Plants', 'render_type' => 'emoji', 'base' => '☘️', 'target' => '🍀'],
        ['label' => 'Plants', 'render_type' => 'emoji', 'base' => '🌲', 'target' => '🌳'],
        ['label' => 'Plants', 'render_type' => 'emoji', 'base' => '🌸', 'target' => '🌺'],

        // =========================
        // Fruit (zelfde “fruit” categorie, klein kleurverschil)
        // =========================
        ['label' => 'Fruit', 'render_type' => 'emoji', 'base' => '🍋', 'target' => '🍊'],
        ['label' => 'Fruit', 'render_type' => 'emoji', 'base' => '🍓', 'target' => '🍒'],
        ['label' => 'Fruit', 'render_type' => 'emoji', 'base' => '🍑', 'target' => '🍐'],

        // =========================
        // Animals (zelfde “type”, niet te obvious)
        // =========================
        ['label' => 'Animals', 'render_type' => 'emoji', 'base' => '🐟', 'target' => '🐠'],
        ['label' => 'Animals', 'render_type' => 'emoji', 'base' => '🦋', 'target' => '🐞'],
        ['label' => 'Animals', 'render_type' => 'emoji', 'base' => '🐶', 'target' => '🐕'],
    ];
}

    private function dailyPuzzle(Carbon $date, int $userId): array
    {
        $pool = $this->pool();
        $poolCount = max(1, count($pool));

        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $idx = $seed % $poolCount;
        $pick = $pool[$idx];

        // ✅ difficulty
        $count = 500;

        // ✅ target index must match count
        $seed2 = crc32('T|' . self::GAME_KEY . '|' . $date->toDateString());
        $targetIndex = (int) ($seed2 % $count);

        // Layout seed: per user per dag
        $layoutSeed = crc32('L|' . self::GAME_KEY . '|' . $date->toDateString() . '|' . $userId);

        $number = 100 + ($seed % 900);

        return [
            'number'       => $number,
            'date'         => $date->toDateString(),
            'label'        => (string) ($pick['label'] ?? 'Find The Emoji'),
            'render_type'  => (string) ($pick['render_type'] ?? 'emoji'),
            'base'         => (string) ($pick['base'] ?? '😄'),
            'target'       => (string) ($pick['target'] ?? '😃'),

            'count'        => $count,
            'target_index' => $targetIndex,

            // render params
            'size'         => 30,
            'speed'        => 1.35,
            'noise'        => 0.45,
            'layout_seed'  => $layoutSeed,
        ];
    }

    private function initialState(): array
    {
        return [
            'started_ms' => now()->getTimestampMs(),
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

    /**
     * ✅ Returns leaderboard rows + my_rank for a scope, optionally based on your current duration.
     */
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

        $puzzle = $this->dailyPuzzle($today, (int) $user->id);

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
            $run->state = $state;
            $run->save();
        }

        if (!$run->state) {
            $run->state = $state;
            $run->save();
        }

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.find-the-emoji', [
            'user'     => $user,
            'puzzle'   => $puzzle,
            'run'      => $run,
            'state'    => $state,

            'scope'    => $lb['scope'],
            'tabs'     => $tabs,
            'topTimes' => collect($lb['rows']), // used for server fallback in Blade
            'myRank'   => $lb['my_rank'],

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

        // already solved
        if ($run->solved) {
            if ($this->wantsJson($request)) {
                $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);

                $streak = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);

                return response()->json([
                    'ok' => true,
                    'solved' => true,
                    'final_time' => $this->mmss($run->duration_ms),
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $state = $run->state ?: $this->initialState();

        $run->solved = true;
        $run->finished_at = now();

        $nowMs = now()->getTimestampMs();
        $startedMs = (int) ($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? $nowMs));
        $run->duration_ms = max(0, $nowMs - $startedMs);
        $run->attempts = 1;

        // reward logic (zelfde als WordForge)
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
                'leaderboard' => $lb,
                'streak' => $streak,
            ]);
        }

        return back();
    }
}