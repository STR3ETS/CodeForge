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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
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
                'title' => 'Play ' . $playGoal . ' games',
                'desc' => 'Finish ' . $playGoal . ' rounds today.',
                'icon' => 'fa-solid fa-gamepad',
                'goal' => $playGoal,
                'reward_xp' => 150,
                'tag' => 'Easy',
                'type' => 'plays',
            ],
            [
                'key' => 'win_1_game',
                'title' => 'Win 1 game',
                'desc' => 'Get at least one win.',
                'icon' => 'fa-solid fa-trophy',
                'goal' => 1,
                'reward_xp' => 250,
                'tag' => 'Medium',
                'type' => 'wins',
            ],
            [
                'key' => 'keep_streak_alive',
                'title' => 'Keep your streak alive',
                'desc' => 'Play any game today.',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'goal' => 1,
                'reward_xp' => 150,
                'tag' => 'XP',
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

    public function leaderboard(Request $request)
    {
        $me = $request->user();

        // scope: global | friends | nl | eu
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends', 'nl', 'eu'], true)) {
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

        // ✅ Placeholders (streak & games) – respects scope
        $usersForOtherBoards = (clone $baseQuery)
            ->orderByDesc('level')
            ->orderByDesc('xp')
            ->limit(10)
            ->get();

        $streakMap = [
            1 => 365,
            2 => 120,
            3 => 90,
            4 => 45,
        ];

        $gamesMap = [
            1 => 842,
            2 => 612,
            3 => 410,
            4 => 128,
        ];

        $topStreaks = $usersForOtherBoards->map(function ($u) use ($streakMap) {
            $value = $streakMap[$u->id] ?? (10 + (($u->id * 7) % 70));
            return ['user' => $u, 'value' => (int) $value];
        })->sortByDesc('value')->values();

        $topGames = $usersForOtherBoards->map(function ($u) use ($gamesMap) {
            $value = $gamesMap[$u->id] ?? (20 + (($u->id * 13) % 300));
            return ['user' => $u, 'value' => (int) $value];
        })->sortByDesc('value')->values();

        return view('dashboard.leaderboard', [
            'user' => $me,
            'scope' => $scope,
            'topLevels' => $topLevels,
            'topStreaks' => $topStreaks,
            'topGames' => $topGames,
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
            ->where('game_key', 'tetris')
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

        // Daily games list
        $games = [
            [
                'key' => 'find-the-emoji',
                'title' => 'Find The Emoji',
                'desc' => 'Find the odd one out as fast as possible.',
                'icon' => 'fa-solid fa-magnifying-glass',
                'tag' => 'Daily Game',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.findtheemoji'),
                'time' => '~20 sec',
                'reward_xp' => 150, // ✅
                'number' => 100 + (crc32('find-the-emoji|' . $today->toDateString()) % 900),
                'status' => $fteSolved ? 'solved' : ($fteFailed ? 'failed' : null),
                'status_time' => $fteTime,
            ],
            [
                'key' => 'word-forge',
                'title' => 'WordForge',
                'desc' => 'Guess the word using the category hint.',
                'icon' => 'fa-solid fa-font',
                'tag' => 'Daily Game',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.wordforge'),
                'time' => '~60 sec',
                'reward_xp' => 150, // ✅
                'number' => 347,
                'status' => $wfSolved ? 'solved' : ($wfFailed ? 'failed' : null),
                'status_time' => $wfTime,
            ],
            [
                'key' => 'sequence-rush',
                'title' => 'Sequence Rush',
                'desc' => 'Pick the missing number in the sequence.',
                'icon' => 'fa-solid fa-list-ol',
                'tag' => 'Daily Game',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.sequence'),
                'time' => '~45 sec',
                'reward_xp' => 150,
                'number' => 100 + (crc32('sequence-rush|' . $today->toDateString()) % 900),
                'status' => $seqSolved ? 'solved' : ($seqFailed ? 'failed' : null),
                'status_time' => $seqTime,
            ],
            [
                'key' => 'flag-guess',
                'title' => 'Flag Guess',
                'desc' => 'Identify the country by its flag.',
                'icon' => 'fa-solid fa-flag',
                'tag' => 'Daily Game',
                'proOnly' => false,
                'available' => true,
                'href' => route('games.flagguess'),
                'time' => '~30 sec',
                'reward_xp' => 150,
                'number' => 100 + (abs(crc32('flag-guess|' . $today->toDateString())) % 900),
                'status' => $fgSolved ? 'solved' : ($fgFailed ? 'failed' : null),
                'status_time' => $fgTime,
            ],
            [
                'key'        => 'tetris',
                'title'      => 'Tetris',
                'desc'       => 'Clear 10 lines as fast as possible.',
                'icon'       => 'fa-solid fa-table-cells',
                'tag'        => 'Daily Game',
                'proOnly'    => false,
                'available'  => true,
                'href'       => route('games.tetris'),
                'time'       => '~2 min',
                'reward_xp'  => 150,
                'number'     => 100 + (abs(crc32('tetris|' . $today->toDateString())) % 900),
                'status'     => $ttSolved ? 'solved' : ($ttFailed ? 'failed' : null),
                'status_time' => $ttTime,
            ],
        ];

        $gameKeys = collect($games)->pluck('key')->filter()->values()->all();
        $q = $this->buildDailyQuestsForUser($user, $today, $gameKeys);

        return view('dashboard.daily', [
            'user' => $user,
            'isPro' => $isPro,
            'limit' => $limit,
            'done' => $done,
            'remaining' => $remaining,
            'games' => $games,
            'quests' => $q['quests'],
            'questsAllDone' => $q['all_done'],
            'questsAllClaimed' => $q['all_claimed'],
            'globalStreak' => $globalStreak,
        ]);
    }

    private function dailyQuestDefinitions(int $playGoal): array
    {
        return [
            [
                'key' => 'play_games',
                'title' => 'Play ' . $playGoal . ' games',
                'desc' => 'Finish ' . $playGoal . ' rounds today.',
                'icon' => 'fa-solid fa-gamepad',
                'goal' => $playGoal,
                'reward_xp' => 150,
                'tag' => 'Easy',
                'type' => 'plays',
            ],
            [
                'key' => 'win_1_game',
                'title' => 'Win 1 game',
                'desc' => 'Get at least one win.',
                'icon' => 'fa-solid fa-trophy',
                'goal' => 1,
                'reward_xp' => 250,
                'tag' => 'Medium',
                'type' => 'wins',
            ],
            [
                'key' => 'keep_streak_alive',
                'title' => 'Keep your streak alive',
                'desc' => 'Play any game today.',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'goal' => 1,
                'reward_xp' => 150,
                'tag' => 'XP',
                'type' => 'any_play',
            ],
        ];
    }

    private function buildDailyQuestsForUser($user, Carbon $today, array $gameKeys): array
    {
        // ✅ “Played today” = attempts>0 OR finished_at OR solved
        $playedRuns = DailyGameRun::query()
            ->where('user_id', $user->id)
            ->where('puzzle_date', $today->toDateString())
            ->whereIn('game_key', $gameKeys)
            ->where(function ($q) {
                $q->where('solved', true)
                ->orWhereNotNull('finished_at')
                ->orWhere('attempts', '>', 0);
            })
            ->get(['id', 'game_key', 'solved', 'finished_at', 'attempts']);

        $plays = (int) $playedRuns->count();
        $wins  = (int) $playedRuns->where('solved', true)->count();

        $playGoal = min(3, max(1, count($gameKeys)));
        $defs = $this->dailyQuestDefinitions($playGoal);

        $claimedKeys = DailyQuestClaim::query()
            ->where('user_id', $user->id)
            ->where('quest_date', $today->toDateString())
            ->pluck('quest_key')
            ->all();

        $quests = [];
        foreach ($defs as $q) {
            $progress = 0;

            if ($q['type'] === 'plays') {
                $progress = $plays;
            } elseif ($q['type'] === 'wins') {
                $progress = $wins;
            } elseif ($q['type'] === 'any_play') {
                $progress = $plays > 0 ? 1 : 0;
            }

            $progress = min((int) $q['goal'], (int) $progress);
            $isDone = $progress >= (int) $q['goal'];
            $claimed = in_array($q['key'], $claimedKeys, true);

            $quests[] = [
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
        }

        $allDone = collect($quests)->every(fn ($qq) => (bool)$qq['is_done']);
        $allClaimedOrNotDone = collect($quests)->every(fn ($qq) => !$qq['is_done'] || $qq['claimed']);

        return [
            'quests' => $quests,
            'all_done' => $allDone,
            'all_claimed' => $allClaimedOrNotDone,
            'plays' => $plays,
            'wins' => $wins,
        ];
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

        $request->validate([
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'profile_banner'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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
            $handle('profile_banner',  'profile/banners', 'profile_banner');
            $handle('profile_picture', 'profile/avatars', 'profile_picture');

            if (!$didSomething) {
                return back()->withErrors(['media' => 'Choose an image to upload.']);
            }

            $user->save();

            Log::debug('[PROFILE_MEDIA_MOVE] saved user', [
                'rid' => $rid,
                'profile_banner' => $user->profile_banner,
                'profile_picture' => $user->profile_picture,
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

        $allowed = ['word-forge', 'find-the-emoji', 'sequence-rush', 'flag-guess', 'tetris'];
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

        return response()->json(['ok' => true]);
    }
}