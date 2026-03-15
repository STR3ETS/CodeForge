<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use App\Models\DailyGameRun;
use App\Models\DailyQuestClaim;
use App\Models\ScorePost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\DailyGameStreakService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Reset daily count when date changes
        $todayStr = now()->toDateString();
        if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
            $user->daily_challenges_done = 0;
            $user->daily_challenges_date = $todayStr;
            $user->save();
        }

        $limit = $user->plan === 'pro' ? null : 5;
        $remaining = is_null($limit) ? null : max(0, $limit - (int) $user->daily_challenges_done);

        $today = now()->startOfDay();

        // ✅ Games played stats (based on runs)
        $playedBase = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('solved', true)
                ->orWhereNotNull('finished_at')
                ->orWhere('attempts', '>', 0);
            });

        $gamesPlayedTotal = (clone $playedBase)->count();

        $weekStart = now()->subDays(6)->startOfDay()->toDateString(); // laatste 7 dagen incl vandaag
        $gamesPlayedWeek = (clone $playedBase)
            ->where('puzzle_date', '>=', $weekStart)
            ->count();

        // ✅ Best rank (level desc, xp desc)
        $bestRank = 1 + \App\Models\User::query()
            ->where(function ($q) use ($user) {
                $q->where('level', '>', (int) $user->level)
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('level', (int) $user->level)
                        ->where('xp', '>', (int) $user->xp);
                });
            })
            ->count();

        // ✅ Daily quests (today) — safe fallback if table not migrated yet
        $quests = [];
        $questsAllDone = false;
        $questsAllClaimed = false;

        $gameKeys = ['find-the-emoji', 'word-forge', 'sequence-rush'];// nu hardcoded, later kun je dit dynamisch maken

        $playsToday = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('puzzle_date', $today->toDateString())
            ->whereIn('game_key', $gameKeys)
            ->where(function ($q) {
                $q->where('solved', true)
                ->orWhereNotNull('finished_at')
                ->orWhere('attempts', '>', 0);
            })
            ->get(['game_key', 'solved']);

        $playsCount = (int) $playsToday->count();
        $winsCount  = (int) $playsToday->where('solved', true)->count();

        // Play-goal is automatisch haalbaar (nu 2 games => goal 2, later 3 games => goal 3)
        $playGoal = min(3, max(1, count($gameKeys)));

        $defs = [
            [
                'key' => 'play_games',
                'title' => 'Speel ' . $playGoal . ' spellen',
                'desc' => 'Voltooi ' . $playGoal . ' rondes vandaag.',
                'icon' => 'fa-solid fa-gamepad',
                'goal' => $playGoal,
                'reward_xp' => 15,
                'tag' => 'Easy',
                'type' => 'plays',
            ],
            [
                'key' => 'win_1_game',
                'title' => 'Win 1 spel',
                'desc' => 'Behaal minstens één overwinning.',
                'icon' => 'fa-solid fa-trophy',
                'goal' => 1,
                'reward_xp' => 50,
                'tag' => 'Medium',
                'type' => 'wins',
            ],
            [
                'key' => 'keep_streak_alive',
                'title' => 'Houd je streak in leven',
                'desc' => 'Speel vandaag een willekeurig spel.',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'goal' => 1,
                'reward_xp' => 25,
                'tag' => 'Easy',
                'type' => 'any_play',
            ],
        ];

        $claimedKeys = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('daily_quest_claims')) {
            $claimedKeys = \App\Models\DailyQuestClaim::query()
                ->where('user_id', $user->id)
                ->where('quest_date', $today->toDateString())
                ->pluck('quest_key')
                ->all();
        }

        $quests = collect($defs)->map(function ($q) use ($playsCount, $winsCount, $claimedKeys) {
            $progress = 0;

            if ($q['type'] === 'plays') $progress = $playsCount;
            if ($q['type'] === 'wins') $progress = $winsCount;
            if ($q['type'] === 'any_play') $progress = $playsCount > 0 ? 1 : 0;

            $progress = min((int) $q['goal'], (int) $progress);
            $isDone = $progress >= (int) $q['goal'];
            $claimed = in_array($q['key'], $claimedKeys, true);

            return [
                'key' => $q['key'],
                'title' => $q['title'],
                'desc' => $q['desc'],
                'icon' => $q['icon'],
                'progress' => $progress,
                'goal' => (int) $q['goal'],
                'reward_xp' => (int) $q['reward_xp'],
                'reward' => '+' . (int) $q['reward_xp'] . ' XP',
                'tag' => $q['tag'],
                'is_done' => $isDone,
                'claimed' => $claimed,
            ];
        })->values()->all();

        $questsAllDone = collect($quests)->every(fn ($q) => (bool) $q['is_done']);
        $questsAllClaimed = collect($quests)->every(fn ($q) => !(bool)$q['is_done'] || (bool)$q['claimed']);

        return view('dashboard.index', [
            'user' => $user,
            'limit' => $limit,
            'remaining' => $remaining,

            'quests' => $quests,
            'questsAllDone' => $questsAllDone,
            'questsAllClaimed' => $questsAllClaimed,

            'stats' => [
                'games_played_total' => $gamesPlayedTotal,
                'games_played_week' => $gamesPlayedWeek,
                'best_rank' => $bestRank,
            ],
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        $limit = $user->plan === 'pro' ? null : 5;
        $remaining = is_null($limit) ? null : max(0, $limit - (int) $user->daily_challenges_done);

        $playedBase = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('solved', true)
                    ->orWhereNotNull('finished_at')
                    ->orWhere('attempts', '>', 0);
            });

        $gamesPlayedTotal = (clone $playedBase)->count();

        $weekStart = now()->subDays(6)->startOfDay()->toDateString();
        $gamesPlayedWeek = (clone $playedBase)
            ->where('puzzle_date', '>=', $weekStart)
            ->count();

        $bestRank = 1 + \App\Models\User::query()
            ->where(function ($q) use ($user) {
                $q->where('level', '>', (int) $user->level)
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('level', (int) $user->level)
                            ->where('xp', '>', (int) $user->xp);
                    });
            })
            ->count();

        $scorePosts = ScorePost::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('dashboard.profile', [
            'user' => $user,
            'limit' => $limit,
            'remaining' => $remaining,
            'stats' => [
                'games_played_total' => $gamesPlayedTotal,
                'games_played_week' => $gamesPlayedWeek,
                'best_rank' => $bestRank,
            ],
            'scorePosts' => $scorePosts,
        ]);
    }

    public function settings()
    {
        return view('dashboard.settings');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return response()->json(['ok' => true]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['ok' => false, 'error' => 'Huidig wachtwoord is onjuist.'], 422);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json(['ok' => true]);
    }

    public function leaderboard(Request $request)
    {
        $me = $request->user();

        // scope: global | friends | nl | eu
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends'], true)) {
            $scope = 'global';
        }

        $baseQuery = User::query()->select('id', 'name', 'profile_picture', 'plan', 'level', 'xp');

        // ✅ Scope filtering (safe fallbacks)
        if ($scope === 'friends') {
            // If you later add a friends relation, this will work.
            if (method_exists($me, 'friends')) {
                $friendIds = $me->friends()->pluck('users.id')->all();
                $ids = array_values(array_unique(array_merge([$me->id], $friendIds)));
                $baseQuery->whereIn('id', $ids);
            } else {
                // Fallback: only yourself (so it never crashes)
                $baseQuery->where('id', $me->id);
            }
        }

        if ($scope === 'nl') {
            if (Schema::hasColumn('users', 'country_code')) {
                $baseQuery->where('country_code', 'NL');
            } elseif (Schema::hasColumn('users', 'country')) {
                $baseQuery->where('country', 'Netherlands');
            } elseif (Schema::hasColumn('users', 'locale')) {
                $baseQuery->where('locale', 'nl');
            } else {
                // fallback global
            }
        }

        if ($scope === 'eu') {
            if (Schema::hasColumn('users', 'region')) {
                $baseQuery->where('region', 'EU');
            }
            // fallback global
        }

        // ✅ Highest Level (live)
        $topLevels = (clone $baseQuery)
            ->orderByDesc('level')
            ->orderByDesc('xp')
            ->limit(10)
            ->get();

        // ✅ Speed leaderboards per game
        $gameMeta = [
            'find-the-emoji' => ['title' => 'Vind de Emoji',   'icon' => 'fa-solid fa-magnifying-glass'],
            'word-forge'     => ['title' => 'Woord Raden',       'icon' => 'fa-solid fa-font'],
            'sequence-rush'  => ['title' => 'Voltooi Reeks',    'icon' => 'fa-solid fa-list-ol'],
            'flag-guess'     => ['title' => 'Vlag Raden',       'icon' => 'fa-solid fa-flag'],
            'block-drop'     => ['title' => 'Blok Drop',        'icon' => 'fa-solid fa-table-cells'],
            'sudoku'         => ['title' => 'Mini Sudoku',      'icon' => 'fa-solid fa-table-cells-large'],
            'memory-grid'    => ['title' => 'Memory Grid',      'icon' => 'fa-solid fa-brain'],
            'color-match'    => ['title' => 'Color Match',      'icon' => 'fa-solid fa-palette'],
            'reaction-time'  => ['title' => 'Reaction Time',    'icon' => 'fa-solid fa-bolt'],
            'maze-runner'    => ['title' => 'Maze Runner',      'icon' => 'fa-solid fa-route'],
            'color-sort'     => ['title' => 'Color Sort',       'icon' => 'fa-solid fa-layer-group'],
        ];

        $speedBoards = [];
        foreach ($gameMeta as $gameKey => $meta) {
            // Reaction Time ranks by lowest average ms
            if ($gameKey === 'reaction-time') {
                $rows = DailyGameRun::query()
                    ->select('user_id', DB::raw('MIN(duration_ms) as best_ms'))
                    ->where('game_key', $gameKey)
                    ->where('solved', true)
                    ->whereNotNull('duration_ms')
                    ->where('duration_ms', '>', 0)
                    ->whereDate('puzzle_date', Carbon::today())
                    ->groupBy('user_id')
                    ->orderBy('best_ms')
                    ->limit(10)
                    ->get();

                $userIds = $rows->pluck('user_id');
                $users = User::whereIn('id', $userIds)
                    ->select('id', 'name', 'profile_picture', 'plan', 'level')
                    ->get()->keyBy('id');

                $speedBoards[$gameKey] = [
                    'title' => $meta['title'],
                    'icon'  => $meta['icon'],
                    'rank_by' => 'reaction',
                    'rows'  => $rows->map(fn($r) => [
                        'user'    => $users->get($r->user_id),
                        'best_ms' => (int) $r->best_ms,
                    ])->filter(fn($r) => $r['user'] !== null)->values(),
                ];
            }
            // Memory Grid ranks by fewest attempts, not fastest time
            elseif ($gameKey === 'memory-grid') {
                $rows = DailyGameRun::query()
                    ->select('user_id', DB::raw('MIN(attempts) as best_attempts'), DB::raw('MIN(duration_ms) as best_ms'))
                    ->where('game_key', $gameKey)
                    ->where('solved', true)
                    ->where('attempts', '>', 0)
                    ->whereDate('puzzle_date', Carbon::today())
                    ->groupBy('user_id')
                    ->orderBy('best_attempts')
                    ->orderBy('best_ms')
                    ->limit(10)
                    ->get();

                $userIds = $rows->pluck('user_id');
                $users = User::whereIn('id', $userIds)
                    ->select('id', 'name', 'profile_picture', 'plan', 'level')
                    ->get()->keyBy('id');

                $speedBoards[$gameKey] = [
                    'title' => $meta['title'],
                    'icon'  => $meta['icon'],
                    'rank_by' => 'attempts',
                    'rows'  => $rows->map(fn($r) => [
                        'user'          => $users->get($r->user_id),
                        'best_ms'       => (int) $r->best_ms,
                        'best_attempts' => (int) $r->best_attempts,
                    ])->filter(fn($r) => $r['user'] !== null)->values(),
                ];
            } else {
                $rows = DailyGameRun::query()
                    ->select('user_id', DB::raw('MIN(duration_ms) as best_ms'))
                    ->where('game_key', $gameKey)
                    ->where('solved', true)
                    ->whereNotNull('duration_ms')
                    ->where('duration_ms', '>', 0)
                    ->whereDate('puzzle_date', Carbon::today())
                    ->groupBy('user_id')
                    ->orderBy('best_ms')
                    ->limit(10)
                    ->get();

                $userIds = $rows->pluck('user_id');
                $users = User::whereIn('id', $userIds)
                    ->select('id', 'name', 'profile_picture', 'plan', 'level')
                    ->get()->keyBy('id');

                $speedBoards[$gameKey] = [
                    'title' => $meta['title'],
                    'icon'  => $meta['icon'],
                    'rank_by' => 'time',
                    'rows'  => $rows->map(fn($r) => [
                        'user'    => $users->get($r->user_id),
                        'best_ms' => (int) $r->best_ms,
                    ])->filter(fn($r) => $r['user'] !== null)->values(),
                ];
            }
        }

        return view('dashboard.leaderboard', [
            'user'        => $me,
            'scope'       => $scope,
            'topLevels'   => $topLevels,
            'speedBoards' => $speedBoards,
        ]);
    }

    public function dailyChallenges(Request $request)
    {
        $user = $request->user();

        // Reset daily count when date changes
        $today = now()->toDateString();
        if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $today) {
            $user->daily_challenges_done = 0;
            $user->daily_challenges_date = $today;
            $user->save();
        }

        $isPro = $user->plan === 'pro';

        // Free users: max 5 per day, Pro: unlimited
        $limit = $isPro ? null : 5;
        $done = (int) $user->daily_challenges_done;
        $remaining = is_null($limit) ? null : max(0, $limit - $done);

        // ✅ Today runs for status labels in the list
        $today = now()->startOfDay();

        $globalStreak = app(DailyGameStreakService::class)
            ->uiPayloadGlobal($user, $today);

        // WordForge status (bestaand)
        $wfRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'word-forge')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $wfSolved = (bool)($wfRun?->solved);
        $wfFailed = (!$wfSolved && !empty($wfRun?->finished_at));
        $wfTime = null;
        if ($wfSolved && $wfRun?->duration_ms !== null) {
            $sec = (int) round($wfRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $wfTime = $mm . ':' . $ss;
        }

        // ✅ Find The Emoji status
        $fteRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'find-the-emoji')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $fteSolved = (bool)($fteRun?->solved);
        $fteFailed = (!$fteSolved && !empty($fteRun?->finished_at));
        $fteTime = null;
        if ($fteSolved && $fteRun?->duration_ms !== null) {
            $sec = (int) round($fteRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $fteTime = $mm . ':' . $ss;
        }

        $seqRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'sequence-rush')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $seqSolved = (bool)($seqRun?->solved);
        $seqFailed = (!$seqSolved && !empty($seqRun?->finished_at));
        $seqTime = null;

        if ($seqSolved && $seqRun?->duration_ms !== null) {
            $sec = (int) round($seqRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $seqTime = $mm . ':' . $ss;
        }

        $fgRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'flag-guess')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $fgSolved = (bool)($fgRun?->solved);
        $fgFailed = (!$fgSolved && !empty($fgRun?->finished_at));
        $fgTime = null;

        if ($fgSolved && $fgRun?->duration_ms !== null) {
            $sec = (int) round($fgRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $fgTime = $mm . ':' . $ss;
        }

        $ttRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'block-drop')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $ttSolved = (bool)($ttRun?->solved);
        $ttFailed = (!$ttSolved && !empty($ttRun?->finished_at));
        $ttTime = null;

        if ($ttSolved && $ttRun?->duration_ms !== null) {
            $sec = (int) round($ttRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $ttTime = $mm . ':' . $ss;
        }

        $sdkRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'sudoku')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $sdkSolved = (bool)($sdkRun?->solved);
        $sdkFailed = (!$sdkSolved && !empty($sdkRun?->finished_at));
        $sdkTime = null;

        if ($sdkSolved && $sdkRun?->duration_ms !== null) {
            $sec = (int) round($sdkRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $sdkTime = $mm . ':' . $ss;
        }

        $mgRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'memory-grid')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $mgSolved = (bool)($mgRun?->solved);
        $mgFailed = (!$mgSolved && !empty($mgRun?->finished_at));
        $mgTime = null;

        if ($mgSolved && $mgRun?->duration_ms !== null) {
            $sec = (int) round($mgRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $mgTime = $mm . ':' . $ss;
        }

        $cmRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'color-match')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $cmSolved = (bool)($cmRun?->solved);
        $cmFailed = (!$cmSolved && !empty($cmRun?->finished_at));
        $cmTime = null;

        if ($cmSolved && $cmRun?->duration_ms !== null) {
            $sec = (int) round($cmRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $cmTime = $mm . ':' . $ss;
        }

        $rtRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'reaction-time')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $rtSolved = (bool)($rtRun?->solved);
        $rtFailed = (!$rtSolved && !empty($rtRun?->finished_at));
        $rtTime = null;

        if ($rtSolved && $rtRun?->duration_ms !== null) {
            $rtTime = ((int) $rtRun->duration_ms) . 'ms';
        }

        $mrRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'maze-runner')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $mrSolved = (bool)($mrRun?->solved);
        $mrFailed = (!$mrSolved && !empty($mrRun?->finished_at));
        $mrTime = null;

        if ($mrSolved && $mrRun?->duration_ms !== null) {
            $sec = (int) round($mrRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $mrTime = $mm . ':' . $ss;
        }

        $csRun = \App\Models\DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('game_key', 'color-sort')
            ->where('puzzle_date', $today->toDateString())
            ->first();

        $csSolved = (bool)($csRun?->solved);
        $csFailed = (!$csSolved && !empty($csRun?->finished_at));
        $csTime = null;

        if ($csSolved && $csRun?->duration_ms !== null) {
            $sec = (int) round($csRun->duration_ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            $csTime = $mm . ':' . $ss;
        }

        // Daily games list
        $games = [
            [
                'key' => 'find-the-emoji',
                'title' => 'Vind de Emoji',
                'desc' => 'Vind de vreemde emoji zo snel mogelijk.',
                'icon' => 'fa-solid fa-magnifying-glass',
                'tag' => 'Daily Game',
                'difficulty' => 'Easy',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.findtheemoji'),
                'time' => '~20 sec',
                'reward_xp' => 25,
                'number' => 100 + (crc32('find-the-emoji|' . $today->toDateString()) % 900),
                'status' => $fteSolved ? 'solved' : ($fteFailed ? 'failed' : null),
                'status_time' => $fteTime,
            ],
            [
                'key' => 'word-forge',
                'title' => 'Woord Raden',
                'desc' => 'Raad het woord met behulp van de categoriehint.',
                'icon' => 'fa-solid fa-font',
                'tag' => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.wordforge'),
                'time' => '~60 sec',
                'reward_xp' => 50,
                'number' => 347,
                'status' => $wfSolved ? 'solved' : ($wfFailed ? 'failed' : null),
                'status_time' => $wfTime,
            ],
            [
                'key' => 'sequence-rush',
                'title' => 'Voltooi Reeks',
                'desc' => 'Vul het ontbrekende getal in de reeks in.',
                'icon' => 'fa-solid fa-list-ol',
                'tag' => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.sequence'),
                'time' => '~45 sec',
                'reward_xp' => 50,
                'number' => 100 + (crc32('sequence-rush|' . $today->toDateString()) % 900),
                'status' => $seqSolved ? 'solved' : ($seqFailed ? 'failed' : null),
                'status_time' => $seqTime,
            ],
            [
                'key' => 'flag-guess',
                'title' => 'Vlag Raden',
                'desc' => 'Identificeer het land aan de hand van zijn vlag.',
                'icon' => 'fa-solid fa-flag',
                'tag' => 'Daily Game',
                'difficulty' => 'Easy',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.flagguess'),
                'time' => '~30 sec',
                'reward_xp' => 25,
                'number' => 100 + (abs(crc32('flag-guess|' . $today->toDateString())) % 900),
                'status' => $fgSolved ? 'solved' : ($fgFailed ? 'failed' : null),
                'status_time' => $fgTime,
            ],
            [
                'key'        => 'block-drop',
                'title'      => 'Blok Drop',
                'desc'       => 'Wis 10 rijen zo snel mogelijk.',
                'icon'       => 'fa-solid fa-table-cells',
                'tag'        => 'Daily Game',
                'difficulty' => 'Hard',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.blockdrop'),
                'time'       => '~2 min',
                'reward_xp'  => 100,
                'number'     => 100 + (abs(crc32('block-drop|' . $today->toDateString())) % 900),
                'status'     => $ttSolved ? 'solved' : ($ttFailed ? 'failed' : null),
                'status_time' => $ttTime,
            ],
            [
                'key'        => 'sudoku',
                'title'      => 'Mini Sudoku',
                'desc'       => 'Los het 4×4 sudoku puzzel op.',
                'icon'       => 'fa-solid fa-table-cells-large',
                'tag'        => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.sudoku'),
                'time'       => '~90 sec',
                'reward_xp'  => 50,
                'number'     => 100 + (abs(crc32('sudoku|' . $today->toDateString())) % 900),
                'status'     => $sdkSolved ? 'solved' : ($sdkFailed ? 'failed' : null),
                'status_time' => $sdkTime,
            ],
            [
                'key'        => 'memory-grid',
                'title'      => 'Memory Grid',
                'desc'       => 'Onthoud de kaarten en vind alle paren.',
                'icon'       => 'fa-solid fa-brain',
                'tag'        => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.memorygrid'),
                'time'       => '~60 sec',
                'reward_xp'  => 50,
                'number'     => 100 + (abs(crc32('memory-grid|' . $today->toDateString())) % 900),
                'status'     => $mgSolved ? 'solved' : ($mgFailed ? 'failed' : null),
                'status_time' => $mgTime,
            ],
            [
                'key'        => 'color-match',
                'title'      => 'Color Match',
                'desc'       => 'Klik op de kleur van de tekst, niet het woord.',
                'icon'       => 'fa-solid fa-palette',
                'tag'        => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.colormatch'),
                'time'       => '~45 sec',
                'reward_xp'  => 50,
                'number'     => 100 + (abs(crc32('color-match|' . $today->toDateString())) % 900),
                'status'     => $cmSolved ? 'solved' : ($cmFailed ? 'failed' : null),
                'status_time' => $cmTime,
            ],
            [
                'key'        => 'reaction-time',
                'title'      => 'Reaction Time',
                'desc'       => 'Klik zo snel mogelijk als het scherm groen wordt.',
                'icon'       => 'fa-solid fa-bolt',
                'tag'        => 'Daily Game',
                'difficulty' => 'Easy',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.reactiontime'),
                'time'       => '~30 sec',
                'reward_xp'  => 25,
                'number'     => 100 + (abs(crc32('reaction-time|' . $today->toDateString())) % 900),
                'status'     => $rtSolved ? 'solved' : ($rtFailed ? 'failed' : null),
                'status_time' => $rtTime,
            ],
            [
                'key'        => 'maze-runner',
                'title'      => 'Maze Runner',
                'desc'       => 'Navigeer door het doolhof van start naar finish.',
                'icon'       => 'fa-solid fa-route',
                'tag'        => 'Daily Game',
                'difficulty' => 'Medium',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.mazerunner'),
                'time'       => '~60 sec',
                'reward_xp'  => 50,
                'number'     => 100 + (abs(crc32('maze-runner|' . $today->toDateString())) % 900),
                'status'     => $mrSolved ? 'solved' : ($mrFailed ? 'failed' : null),
                'status_time' => $mrTime,
            ],
            [
                'key'        => 'color-sort',
                'title'      => 'Color Sort',
                'desc'       => 'Sorteer gekleurde blokken op kleur.',
                'icon'       => 'fa-solid fa-layer-group',
                'tag'        => 'Daily Game',
                'difficulty' => 'Hard',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.colorsort'),
                'time'       => '~2 min',
                'reward_xp'  => 100,
                'number'     => 100 + (abs(crc32('color-sort|' . $today->toDateString())) % 900),
                'status'     => $csSolved ? 'solved' : ($csFailed ? 'failed' : null),
                'status_time' => $csTime,
            ],
        ];

        $gameKeys     = collect($games)->pluck('key')->filter()->values()->all();
        $q            = $this->buildDailyQuestsForUser($user, $today, $gameKeys);
        $weeklyQuests = $this->buildWeeklyQuestsForUser($user, $today);

        return view('dashboard.daily', [
            'user'              => $user,
            'isPro'             => $isPro,
            'limit'             => $limit,
            'done'              => $done,
            'remaining'         => $remaining,
            'games'             => $games,
            'quests'            => $q['quests'],
            'questsAllDone'     => $q['all_done'],
            'questsAllClaimed'  => $q['all_claimed'],
            'weeklyQuests'      => $weeklyQuests,
            'globalStreak'      => $globalStreak,
        ]);
    }

    private function dailyQuestDefinitions(): array
    {
        return [
            [
                'key' => 'play_3_games',
                'title' => 'Speel 3 spellen',
                'desc' => 'Voltooi 3 rondes vandaag.',
                'icon' => 'fa-solid fa-gamepad',
                'goal' => 3,
                'reward_xp' => 15,
                'tag' => 'Easy',
                'type' => 'plays',
            ],
            [
                'key' => 'win_1_game',
                'title' => 'Win 1 spel',
                'desc' => 'Win minstens één spel vandaag.',
                'icon' => 'fa-solid fa-trophy',
                'goal' => 1,
                'reward_xp' => 50,
                'tag' => 'Medium',
                'type' => 'wins',
            ],
            [
                'key' => 'keep_streak_alive',
                'title' => 'Houd je streak in leven',
                'desc' => 'Speel vandaag een willekeurig spel.',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'goal' => 1,
                'reward_xp' => 25,
                'tag' => 'Easy',
                'type' => 'any_play',
            ],
            [
                'key' => 'win_wordforge',
                'title' => 'Win Woord Raden',
                'desc' => 'Voltooi Woord Raden vandaag.',
                'icon' => 'fa-solid fa-font',
                'goal' => 1,
                'reward_xp' => 25,
                'tag' => 'Medium',
                'type' => 'game_win',
                'game' => 'word-forge',
            ],
            [
                'key' => 'fast_emoji',
                'title' => 'Vind de Emoji in ≤15s',
                'desc' => 'Vind de Emoji in max. 15 seconden.',
                'icon' => 'fa-solid fa-bolt',
                'goal' => 1,
                'reward_xp' => 75,
                'tag' => 'Hard',
                'type' => 'game_win_under',
                'game' => 'find-the-emoji',
                'max_ms' => 15000,
            ],
            [
                'key' => 'win_flag_guess',
                'title' => 'Win Vlag Raden',
                'desc' => 'Raad vandaag een vlag correct.',
                'icon' => 'fa-solid fa-flag',
                'goal' => 1,
                'reward_xp' => 15,
                'tag' => 'Easy',
                'type' => 'game_win',
                'game' => 'flag-guess',
            ],
        ];
    }

    private function weeklyQuestDefinitions(): array
    {
        return [
            [
                'key' => 'w_play_10',
                'title' => 'Speel 10 spellen',
                'desc' => 'Voltooi 10 rondes deze week.',
                'icon' => 'fa-solid fa-gamepad',
                'goal' => 10,
                'reward_xp' => 50,
                'tag' => 'Medium',
                'type' => 'weekly_plays',
            ],
            [
                'key' => 'w_win_5',
                'title' => 'Win 5 spellen',
                'desc' => 'Win minstens 5 spellen deze week.',
                'icon' => 'fa-solid fa-trophy',
                'goal' => 5,
                'reward_xp' => 75,
                'tag' => 'Hard',
                'type' => 'weekly_wins',
            ],
            [
                'key' => 'w_wordforge_3',
                'title' => 'Win Woord Raden 3×',
                'desc' => 'Voltooi Woord Raden 3× deze week.',
                'icon' => 'fa-solid fa-font',
                'goal' => 3,
                'reward_xp' => 40,
                'tag' => 'Medium',
                'type' => 'weekly_game_wins',
                'game' => 'word-forge',
            ],
            [
                'key' => 'w_sequence_3',
                'title' => 'Win Voltooi Reeks 3×',
                'desc' => 'Win Voltooi Reeks 3× deze week.',
                'icon' => 'fa-solid fa-list-ol',
                'goal' => 3,
                'reward_xp' => 40,
                'tag' => 'Medium',
                'type' => 'weekly_game_wins',
                'game' => 'sequence-rush',
            ],
            [
                'key' => 'w_blockdrop',
                'title' => 'Voltooi Blok Drop',
                'desc' => 'Win Blok Drop één keer deze week.',
                'icon' => 'fa-solid fa-table-cells',
                'goal' => 1,
                'reward_xp' => 60,
                'tag' => 'Hard',
                'type' => 'weekly_game_wins',
                'game' => 'block-drop',
            ],
            [
                'key' => 'w_all_5_games',
                'title' => 'Speel alle 5 spellen',
                'desc' => 'Speel elk dagelijks spel één keer.',
                'icon' => 'fa-solid fa-layer-group',
                'goal' => 5,
                'reward_xp' => 75,
                'tag' => 'Hard',
                'type' => 'weekly_unique_games',
            ],
        ];
    }

    private function buildDailyQuestsForUser($user, Carbon $today, array $gameKeys = []): array
    {
        $allRuns = DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('puzzle_date', $today->toDateString())
            ->get(['game_key', 'solved', 'finished_at', 'attempts', 'duration_ms']);

        $plays = (int) $allRuns->filter(fn($r) => $r->solved || !empty($r->finished_at) || $r->attempts > 0)->count();
        $wins  = (int) $allRuns->where('solved', true)->count();

        $defs = $this->dailyQuestDefinitions();

        $claimedKeys = DailyQuestClaim::query()
            ->where('user_id', $user->id)
            ->where('quest_date', $today->toDateString())
            ->whereIn('quest_key', collect($defs)->pluck('key')->all())
            ->pluck('quest_key')
            ->all();

        $quests = [];
        foreach ($defs as $q) {
            $progress = 0;

            switch ($q['type']) {
                case 'plays':
                    $progress = $plays;
                    break;
                case 'wins':
                    $progress = $wins;
                    break;
                case 'any_play':
                    $progress = $plays > 0 ? 1 : 0;
                    break;
                case 'game_win':
                    $progress = (int) $allRuns->where('game_key', $q['game'])->where('solved', true)->count();
                    break;
                case 'game_win_under':
                    $maxMs = (int) ($q['max_ms'] ?? 0);
                    $progress = (int) $allRuns
                        ->where('game_key', $q['game'])
                        ->where('solved', true)
                        ->filter(fn($r) => $r->duration_ms !== null && (int)$r->duration_ms <= $maxMs)
                        ->count();
                    break;
            }

            $progress = min((int) $q['goal'], (int) $progress);
            $isDone   = $progress >= (int) $q['goal'];
            $claimed  = in_array($q['key'], $claimedKeys, true);

            $quests[] = [
                'key'        => $q['key'],
                'title'      => $q['title'],
                'desc'       => $q['desc'],
                'icon'       => $q['icon'],
                'progress'   => $progress,
                'goal'       => (int) $q['goal'],
                'reward_xp'  => (int) $q['reward_xp'],
                'reward'     => '+' . (int) $q['reward_xp'] . ' XP',
                'tag'        => $q['tag'],
                'is_done'    => $isDone,
                'claimed'    => $claimed,
                'quest_type' => 'daily',
            ];
        }

        $allDone             = collect($quests)->every(fn($qq) => (bool)$qq['is_done']);
        $allClaimedOrNotDone = collect($quests)->every(fn($qq) => !$qq['is_done'] || $qq['claimed']);

        return [
            'quests'      => $quests,
            'all_done'    => $allDone,
            'all_claimed' => $allClaimedOrNotDone,
            'plays'       => $plays,
            'wins'        => $wins,
        ];
    }

    private function buildWeeklyQuestsForUser($user, Carbon $today): array
    {
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $weekRuns = DailyGameRun::query()
            ->where('user_id', $user->id)
            ->whereBetween('puzzle_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get(['game_key', 'solved', 'finished_at', 'attempts', 'duration_ms']);

        $weekPlays   = (int) $weekRuns->filter(fn($r) => $r->solved || !empty($r->finished_at) || $r->attempts > 0)->count();
        $weekWins    = (int) $weekRuns->where('solved', true)->count();
        $uniqueGames = $weekRuns->where('solved', true)->pluck('game_key')->unique()->count();

        $defs = $this->weeklyQuestDefinitions();

        $claimedKeys = DailyQuestClaim::query()
            ->where('user_id', $user->id)
            ->where('quest_date', $weekStart->toDateString())
            ->whereIn('quest_key', collect($defs)->pluck('key')->all())
            ->pluck('quest_key')
            ->all();

        $quests = [];
        foreach ($defs as $q) {
            $progress = 0;

            switch ($q['type']) {
                case 'weekly_plays':
                    $progress = $weekPlays;
                    break;
                case 'weekly_wins':
                    $progress = $weekWins;
                    break;
                case 'weekly_game_wins':
                    $progress = (int) $weekRuns->where('game_key', $q['game'])->where('solved', true)->count();
                    break;
                case 'weekly_unique_games':
                    $progress = $uniqueGames;
                    break;
            }

            $progress = min((int) $q['goal'], (int) $progress);
            $isDone   = $progress >= (int) $q['goal'];
            $claimed  = in_array($q['key'], $claimedKeys, true);

            $quests[] = [
                'key'        => $q['key'],
                'title'      => $q['title'],
                'desc'       => $q['desc'],
                'icon'       => $q['icon'],
                'progress'   => $progress,
                'goal'       => (int) $q['goal'],
                'reward_xp'  => (int) $q['reward_xp'],
                'reward'     => '+' . (int) $q['reward_xp'] . ' XP',
                'tag'        => $q['tag'],
                'is_done'    => $isDone,
                'claimed'    => $claimed,
                'quest_type' => 'weekly',
            ];
        }

        return $quests;
    }

    public function claimDailyQuests(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();

        // dezelfde gameKeys als daily page
        $gameKeys = ['find-the-emoji', 'word-forge', 'sequence-rush'];

        $q = $this->buildDailyQuestsForUser($user, $today, $gameKeys);

        if (!$q['all_done']) {
            return back()->with('error', 'Finish all quests first.');
        }

        $awardedXp = 0;

        DB::transaction(function () use ($user, $today, $q, &$awardedXp) {
            foreach ($q['quests'] as $quest) {
                if (!$quest['is_done'] || $quest['claimed']) continue;

                DailyQuestClaim::create([
                    'user_id' => $user->id,
                    'quest_key' => $quest['key'],
                    'quest_date' => $today->toDateString(),
                    'reward_xp' => (int) $quest['reward_xp'],
                    'claimed_at' => now(),
                ]);

                $awardedXp += (int) $quest['reward_xp'];
            }

            if ($awardedXp > 0) {
                $user->addXp($awardedXp);
            }
        });

        return back()->with('quest_rewarded', $awardedXp);
    }

    public function claimSingleQuest(Request $request)
    {
        $user      = $request->user();
        $today     = now()->startOfDay();
        $questKey  = (string) $request->input('quest_key', '');
        $questType = (string) $request->input('quest_type', 'daily');

        if ($questType === 'weekly') {
            $quests    = $this->buildWeeklyQuestsForUser($user, $today);
            $questDate = $today->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        } else {
            $q         = $this->buildDailyQuestsForUser($user, $today);
            $quests    = $q['quests'];
            $questDate = $today->toDateString();
        }

        $quest = collect($quests)->firstWhere('key', $questKey);

        if (!$quest || !$quest['is_done'] || $quest['claimed']) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['ok' => false, 'message' => 'Quest cannot be claimed.'], 422);
            }
            return back()->with('error', 'Quest cannot be claimed.');
        }

        try {
            DailyQuestClaim::create([
                'user_id'    => $user->id,
                'quest_key'  => $questKey,
                'quest_date' => $questDate,
                'reward_xp'  => (int) $quest['reward_xp'],
                'claimed_at' => now(),
            ]);
            $user->addXp((int) $quest['reward_xp']);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Already claimed (race condition) — ignore silently
        }

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['ok' => true, 'reward_xp' => (int) $quest['reward_xp']]);
        }

        return back()->with('quest_rewarded', $quest['reward_xp']);
    }

    public function updateProfileMedia(Request $request)
    {
        $rid = (string) Str::uuid();
        $user = $request->user();

        Log::debug('[PROFILE_MEDIA_MOVE] start', [
            'rid' => $rid,
            'user_id' => $user->id,
            'content_type' => $request->header('content-type'),
            'content_length' => $request->header('content-length'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'sys_get_temp_dir' => sys_get_temp_dir(),
        ]);

        $isPro = ($user->plan ?? 'free') === 'pro';
        $avatarMimes = $isPro ? 'jpg,jpeg,png,webp,gif' : 'jpg,jpeg,png,webp';
        $bannerMimes = $isPro ? 'jpg,jpeg,png,webp,gif' : 'jpg,jpeg,png,webp';
        $avatarMax = $isPro ? 5120 : 2048;
        $bannerMax = $isPro ? 8192 : 4096;

        $request->validate([
            'profile_picture' => ['nullable', 'file', 'mimes:' . $avatarMimes, 'max:' . $avatarMax],
            'profile_banner'  => ['nullable', 'file', 'mimes:' . $bannerMimes, 'max:' . $bannerMax],
        ]);

        $base = storage_path('app/public');
        $didSomething = false;

        $handle = function (string $field, string $folder, string $column) use ($request, $user, $base, &$didSomething, $rid) {
            if (!$request->hasFile($field)) return;

            $file = $request->file($field);

            Log::debug('[PROFILE_MEDIA_MOVE] file meta', [
                'rid' => $rid,
                'field' => $field,
                'is_valid' => $file?->isValid(),
                'error' => $file?->getError(),
                'pathname' => $file?->getPathname(),
                'realpath' => $file?->getRealPath(),
                'exists_pathname' => $file ? file_exists($file->getPathname()) : null,
                'readable_pathname' => $file ? is_readable($file->getPathname()) : null,
                'size' => $file?->getSize(),
            ]);

            if (!$file || !$file->isValid() || empty($file->getPathname())) {
                throw new \RuntimeException("Upload file missing/invalid for {$field} (rid: {$rid})");
            }

            // Ensure destination dir exists
            $dir = $base . DIRECTORY_SEPARATOR . trim($folder, '/\\');
            File::ensureDirectoryExists($dir);

            // Delete old file (if any)
            $old = (string) ($user->{$column} ?? '');
            if ($old !== '') {
                $oldAbs = $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $old);
                File::delete($oldAbs);
            }

            // Make safe filename
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $name = (string) Str::uuid() . '.' . $ext;

            // Move
            $file->move($dir, $name);

            // Save relative path for /storage/...
            $user->{$column} = trim($folder, '/\\') . '/' . $name;

            Log::debug('[PROFILE_MEDIA_MOVE] moved ok', [
                'rid' => $rid,
                'field' => $field,
                'saved_path' => $user->{$column},
                'abs' => $dir . DIRECTORY_SEPARATOR . $name,
                'abs_exists' => file_exists($dir . DIRECTORY_SEPARATOR . $name),
            ]);

            $didSomething = true;
        };

        try {
            // Track whether this is the first avatar/banner for XP rewards
            $hadAvatar = !empty($user->profile_picture);
            $hadBanner = !empty($user->profile_banner);

            $handle('profile_banner',  'profile/banners', 'profile_banner');
            $handle('profile_picture', 'profile/avatars', 'profile_picture');

            if (!$didSomething) {
                return back()->withErrors(['media' => 'Choose an image to upload.']);
            }

            $user->save();

            // Award XP for first-time avatar/banner upload
            $xpAwarded = 0;
            if (!$hadAvatar && !empty($user->profile_picture)) {
                $xpAwarded += 100;
            }
            if (!$hadBanner && !empty($user->profile_banner)) {
                $xpAwarded += 100;
            }
            if ($xpAwarded > 0) {
                $user->addXp($xpAwarded);
            }

            Log::debug('[PROFILE_MEDIA_MOVE] saved user', [
                'rid' => $rid,
                'profile_banner' => $user->profile_banner,
                'profile_picture' => $user->profile_picture,
                'xp_awarded' => $xpAwarded,
            ]);

            return back();
        } catch (\Throwable $e) {
            Log::error('[PROFILE_MEDIA_MOVE] exception', [
                'rid' => $rid,
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 2500),
            ]);
            throw $e;
        }
    }

    public function abandonGame(Request $request)
    {
        $user  = $request->user();
        $today = now()->startOfDay();
        $key   = (string) $request->input('game_key', '');

        $allowed = ['word-forge', 'find-the-emoji', 'sequence-rush', 'flag-guess', 'block-drop', 'sudoku', 'memory-grid', 'color-match', 'reaction-time', 'maze-runner', 'color-sort'];
        if (!in_array($key, $allowed, true)) {
            return response()->json(['ok' => false], 422);
        }

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', $key)
            ->where('puzzle_date', $today->toDateString())
            ->first();

        // Already finished or doesn't exist — nothing to do
        if (!$run || $run->solved || !empty($run->finished_at)) {
            return response()->json(['ok' => true]);
        }

        $run->solved      = false;
        $run->finished_at = now();
        $run->save();

        // Count as played game for free-user limit
        $todayStr = now()->toDateString();
        if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
            $user->daily_challenges_done = 0;
            $user->daily_challenges_date = $todayStr;
        }
        $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
        $user->save();

        return response()->json(['ok' => true]);
    }

    public function markGameStarted(Request $request)
    {
        $user  = $request->user();
        $today = now()->startOfDay();
        $key   = (string) $request->input('game_key', '');

        $allowed = ['word-forge', 'find-the-emoji', 'sequence-rush', 'flag-guess', 'block-drop', 'sudoku', 'memory-grid', 'color-match', 'reaction-time', 'maze-runner', 'color-sort'];
        if (!in_array($key, $allowed, true)) {
            return response()->json(['ok' => false], 422);
        }

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', $key)
            ->where('puzzle_date', $today->toDateString())
            ->first();

        if (!$run || $run->solved || !empty($run->finished_at)) {
            return response()->json(['ok' => true]);
        }

        $nowMs = now()->getTimestampMs();
        $state = $run->state ?? [];
        $state['started_ms'] = $nowMs;
        $run->state      = $state;
        $run->started_at = now();
        $run->save();

        return response()->json(['ok' => true, 'started_ms' => $nowMs]);
    }

    private const GAME_NAMES = [
        'find-the-emoji' => 'Vind de Emoji',
        'word-forge'     => 'Woord Raden',
        'sequence-rush'  => 'Voltooi Reeks',
        'flag-guess'     => 'Vlag Raden',
        'block-drop'     => 'Blok Drop',
        'sudoku'         => 'Mini Sudoku',
        'memory-grid'    => 'Memory Grid',
        'color-match'    => 'Color Match',
        'reaction-time'  => 'Reaction Time',
        'maze-runner'    => 'Maze Runner',
        'color-sort'     => 'Color Sort',
    ];

    private const GAME_ROUTES = [
        'find-the-emoji' => 'games.findtheemoji',
        'word-forge'     => 'games.wordforge',
        'sequence-rush'  => 'games.sequence',
        'flag-guess'     => 'games.flagguess',
        'block-drop'     => 'games.blockdrop',
        'sudoku'         => 'games.sudoku',
        'memory-grid'    => 'games.memorygrid',
        'color-match'    => 'games.colormatch',
        'reaction-time'  => 'games.reactiontime',
        'maze-runner'    => 'games.mazerunner',
        'color-sort'     => 'games.colorsort',
    ];

    /**
     * Get default share message + percentile for the share modal.
     */
    public function sharePreview(Request $request)
    {
        $user = $request->user();
        $gameKey = (string) $request->input('game_key');
        $today = now()->startOfDay();

        if (!isset(self::GAME_NAMES[$gameKey])) {
            return response()->json(['ok' => false], 422);
        }

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', $gameKey)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->first();

        if (!$run) {
            return response()->json(['ok' => false], 422);
        }

        $gameName = self::GAME_NAMES[$gameKey];
        $fmtTime = $this->formatMs($run->duration_ms);
        $percentile = $this->calcPercentile($gameKey, $today, $run->duration_ms);

        $msg = "Ik heb vandaag {$gameName} opgelost";
        if ($fmtTime) {
            $msg .= " in {$fmtTime}";
        }
        if ($percentile !== null) {
            $msg .= " en was daarmee sneller dan {$percentile}% van de spelers";
        }
        $msg .= '! 🧠🔥';

        if (isset(self::GAME_ROUTES[$gameKey])) {
            $msg .= "\n\nSpeel zelf: " . route(self::GAME_ROUTES[$gameKey]);
        }

        return response()->json([
            'ok' => true,
            'default_message' => $msg,
            'percentile' => $percentile,
            'formatted_time' => $fmtTime,
        ]);
    }

    public function shareScore(Request $request)
    {
        $user = $request->user();
        $gameKey = (string) $request->input('game_key');
        $message = (string) $request->input('message', '');
        $today = now()->startOfDay();

        if (!isset(self::GAME_NAMES[$gameKey])) {
            return response()->json(['ok' => false, 'error' => 'Onbekend spel'], 422);
        }

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', $gameKey)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->first();

        if (!$run) {
            return response()->json(['ok' => false, 'error' => 'Geen opgeloste game gevonden'], 422);
        }

        $fmtTime = $this->formatMs($run->duration_ms);
        $percentile = $this->calcPercentile($gameKey, $today, $run->duration_ms);

        // Sanitize message
        $message = mb_substr(trim($message), 0, 500);

        $post = ScorePost::updateOrCreate(
            [
                'user_id'     => $user->id,
                'game_key'    => $gameKey,
                'puzzle_date' => $today->toDateString(),
            ],
            [
                'game_name'      => self::GAME_NAMES[$gameKey],
                'solved'         => true,
                'duration_ms'    => $run->duration_ms,
                'attempts'       => $run->attempts,
                'formatted_time' => $fmtTime,
                'message'        => $message ?: null,
                'percentile'     => $percentile,
            ]
        );

        return response()->json(['ok' => true, 'post_id' => $post->id]);
    }

    private function formatMs(?int $ms): ?string
    {
        if (!$ms) return null;
        $sec = (int) round($ms / 1000);
        $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
        $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
        return $mm . ':' . $ss;
    }

    private function calcPercentile(string $gameKey, $today, ?int $durationMs): ?int
    {
        if (!$durationMs) return null;

        $totalToday = DailyGameRun::where('game_key', $gameKey)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->whereNotNull('duration_ms')
            ->count();

        if ($totalToday <= 1) return null;

        $slowerCount = DailyGameRun::where('game_key', $gameKey)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->whereNotNull('duration_ms')
            ->where('duration_ms', '>', $durationMs)
            ->count();

        return (int) round(($slowerCount / $totalToday) * 100);
    }
}