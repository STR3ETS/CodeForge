<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FlagGuessController extends Controller
{
    private const GAME_KEY = 'flag-guess';
    private const TOTAL_QUESTIONS = 3;
    private const MAX_WRONG = 2;
    private const PENALTY_MS_PER_WRONG = 3000;

    private const COUNTRIES = [
        ['code' => 'af', 'name' => 'Afghanistan'],
        ['code' => 'al', 'name' => 'Albania'],
        ['code' => 'dz', 'name' => 'Algeria'],
        ['code' => 'ad', 'name' => 'Andorra'],
        ['code' => 'ao', 'name' => 'Angola'],
        ['code' => 'ar', 'name' => 'Argentina'],
        ['code' => 'am', 'name' => 'Armenia'],
        ['code' => 'au', 'name' => 'Australia'],
        ['code' => 'at', 'name' => 'Austria'],
        ['code' => 'az', 'name' => 'Azerbaijan'],
        ['code' => 'bs', 'name' => 'Bahamas'],
        ['code' => 'bh', 'name' => 'Bahrain'],
        ['code' => 'bd', 'name' => 'Bangladesh'],
        ['code' => 'be', 'name' => 'Belgium'],
        ['code' => 'bz', 'name' => 'Belize'],
        ['code' => 'bj', 'name' => 'Benin'],
        ['code' => 'bt', 'name' => 'Bhutan'],
        ['code' => 'bo', 'name' => 'Bolivia'],
        ['code' => 'ba', 'name' => 'Bosnia and Herzegovina'],
        ['code' => 'bw', 'name' => 'Botswana'],
        ['code' => 'br', 'name' => 'Brazil'],
        ['code' => 'bn', 'name' => 'Brunei'],
        ['code' => 'bg', 'name' => 'Bulgaria'],
        ['code' => 'bf', 'name' => 'Burkina Faso'],
        ['code' => 'bi', 'name' => 'Burundi'],
        ['code' => 'kh', 'name' => 'Cambodia'],
        ['code' => 'cm', 'name' => 'Cameroon'],
        ['code' => 'ca', 'name' => 'Canada'],
        ['code' => 'cv', 'name' => 'Cape Verde'],
        ['code' => 'cf', 'name' => 'Central African Republic'],
        ['code' => 'td', 'name' => 'Chad'],
        ['code' => 'cl', 'name' => 'Chile'],
        ['code' => 'cn', 'name' => 'China'],
        ['code' => 'co', 'name' => 'Colombia'],
        ['code' => 'cd', 'name' => 'Congo (DR)'],
        ['code' => 'cr', 'name' => 'Costa Rica'],
        ['code' => 'hr', 'name' => 'Croatia'],
        ['code' => 'cu', 'name' => 'Cuba'],
        ['code' => 'cy', 'name' => 'Cyprus'],
        ['code' => 'cz', 'name' => 'Czech Republic'],
        ['code' => 'dk', 'name' => 'Denmark'],
        ['code' => 'dj', 'name' => 'Djibouti'],
        ['code' => 'do', 'name' => 'Dominican Republic'],
        ['code' => 'ec', 'name' => 'Ecuador'],
        ['code' => 'eg', 'name' => 'Egypt'],
        ['code' => 'sv', 'name' => 'El Salvador'],
        ['code' => 'ee', 'name' => 'Estonia'],
        ['code' => 'et', 'name' => 'Ethiopia'],
        ['code' => 'fj', 'name' => 'Fiji'],
        ['code' => 'fi', 'name' => 'Finland'],
        ['code' => 'fr', 'name' => 'France'],
        ['code' => 'ga', 'name' => 'Gabon'],
        ['code' => 'gm', 'name' => 'Gambia'],
        ['code' => 'ge', 'name' => 'Georgia'],
        ['code' => 'de', 'name' => 'Germany'],
        ['code' => 'gh', 'name' => 'Ghana'],
        ['code' => 'gr', 'name' => 'Greece'],
        ['code' => 'gt', 'name' => 'Guatemala'],
        ['code' => 'gn', 'name' => 'Guinea'],
        ['code' => 'gy', 'name' => 'Guyana'],
        ['code' => 'ht', 'name' => 'Haiti'],
        ['code' => 'hn', 'name' => 'Honduras'],
        ['code' => 'hu', 'name' => 'Hungary'],
        ['code' => 'is', 'name' => 'Iceland'],
        ['code' => 'in', 'name' => 'India'],
        ['code' => 'id', 'name' => 'Indonesia'],
        ['code' => 'ir', 'name' => 'Iran'],
        ['code' => 'iq', 'name' => 'Iraq'],
        ['code' => 'ie', 'name' => 'Ireland'],
        ['code' => 'il', 'name' => 'Israel'],
        ['code' => 'it', 'name' => 'Italy'],
        ['code' => 'jm', 'name' => 'Jamaica'],
        ['code' => 'jp', 'name' => 'Japan'],
        ['code' => 'jo', 'name' => 'Jordan'],
        ['code' => 'kz', 'name' => 'Kazakhstan'],
        ['code' => 'ke', 'name' => 'Kenya'],
        ['code' => 'kw', 'name' => 'Kuwait'],
        ['code' => 'kg', 'name' => 'Kyrgyzstan'],
        ['code' => 'la', 'name' => 'Laos'],
        ['code' => 'lv', 'name' => 'Latvia'],
        ['code' => 'lb', 'name' => 'Lebanon'],
        ['code' => 'ls', 'name' => 'Lesotho'],
        ['code' => 'lr', 'name' => 'Liberia'],
        ['code' => 'ly', 'name' => 'Libya'],
        ['code' => 'li', 'name' => 'Liechtenstein'],
        ['code' => 'lt', 'name' => 'Lithuania'],
        ['code' => 'lu', 'name' => 'Luxembourg'],
        ['code' => 'mg', 'name' => 'Madagascar'],
        ['code' => 'mw', 'name' => 'Malawi'],
        ['code' => 'my', 'name' => 'Malaysia'],
        ['code' => 'mv', 'name' => 'Maldives'],
        ['code' => 'ml', 'name' => 'Mali'],
        ['code' => 'mt', 'name' => 'Malta'],
        ['code' => 'mr', 'name' => 'Mauritania'],
        ['code' => 'mu', 'name' => 'Mauritius'],
        ['code' => 'mx', 'name' => 'Mexico'],
        ['code' => 'md', 'name' => 'Moldova'],
        ['code' => 'mc', 'name' => 'Monaco'],
        ['code' => 'mn', 'name' => 'Mongolia'],
        ['code' => 'me', 'name' => 'Montenegro'],
        ['code' => 'ma', 'name' => 'Morocco'],
        ['code' => 'mz', 'name' => 'Mozambique'],
        ['code' => 'mm', 'name' => 'Myanmar'],
        ['code' => 'na', 'name' => 'Namibia'],
        ['code' => 'np', 'name' => 'Nepal'],
        ['code' => 'nl', 'name' => 'Netherlands'],
        ['code' => 'nz', 'name' => 'New Zealand'],
        ['code' => 'ni', 'name' => 'Nicaragua'],
        ['code' => 'ne', 'name' => 'Niger'],
        ['code' => 'ng', 'name' => 'Nigeria'],
        ['code' => 'mk', 'name' => 'North Macedonia'],
        ['code' => 'no', 'name' => 'Norway'],
        ['code' => 'om', 'name' => 'Oman'],
        ['code' => 'pk', 'name' => 'Pakistan'],
        ['code' => 'pa', 'name' => 'Panama'],
        ['code' => 'pg', 'name' => 'Papua New Guinea'],
        ['code' => 'py', 'name' => 'Paraguay'],
        ['code' => 'pe', 'name' => 'Peru'],
        ['code' => 'ph', 'name' => 'Philippines'],
        ['code' => 'pl', 'name' => 'Poland'],
        ['code' => 'pt', 'name' => 'Portugal'],
        ['code' => 'qa', 'name' => 'Qatar'],
        ['code' => 'ro', 'name' => 'Romania'],
        ['code' => 'ru', 'name' => 'Russia'],
        ['code' => 'rw', 'name' => 'Rwanda'],
        ['code' => 'sa', 'name' => 'Saudi Arabia'],
        ['code' => 'sn', 'name' => 'Senegal'],
        ['code' => 'rs', 'name' => 'Serbia'],
        ['code' => 'sl', 'name' => 'Sierra Leone'],
        ['code' => 'sg', 'name' => 'Singapore'],
        ['code' => 'sk', 'name' => 'Slovakia'],
        ['code' => 'si', 'name' => 'Slovenia'],
        ['code' => 'so', 'name' => 'Somalia'],
        ['code' => 'za', 'name' => 'South Africa'],
        ['code' => 'ss', 'name' => 'South Sudan'],
        ['code' => 'es', 'name' => 'Spain'],
        ['code' => 'lk', 'name' => 'Sri Lanka'],
        ['code' => 'sd', 'name' => 'Sudan'],
        ['code' => 'sr', 'name' => 'Suriname'],
        ['code' => 'se', 'name' => 'Sweden'],
        ['code' => 'ch', 'name' => 'Switzerland'],
        ['code' => 'sy', 'name' => 'Syria'],
        ['code' => 'tw', 'name' => 'Taiwan'],
        ['code' => 'tj', 'name' => 'Tajikistan'],
        ['code' => 'tz', 'name' => 'Tanzania'],
        ['code' => 'th', 'name' => 'Thailand'],
        ['code' => 'tl', 'name' => 'Timor-Leste'],
        ['code' => 'tg', 'name' => 'Togo'],
        ['code' => 'tt', 'name' => 'Trinidad and Tobago'],
        ['code' => 'tn', 'name' => 'Tunisia'],
        ['code' => 'tr', 'name' => 'Turkey'],
        ['code' => 'tm', 'name' => 'Turkmenistan'],
        ['code' => 'ug', 'name' => 'Uganda'],
        ['code' => 'ua', 'name' => 'Ukraine'],
        ['code' => 'ae', 'name' => 'United Arab Emirates'],
        ['code' => 'gb', 'name' => 'United Kingdom'],
        ['code' => 'us', 'name' => 'United States'],
        ['code' => 'uy', 'name' => 'Uruguay'],
        ['code' => 'uz', 'name' => 'Uzbekistan'],
        ['code' => 've', 'name' => 'Venezuela'],
        ['code' => 'vn', 'name' => 'Vietnam'],
        ['code' => 'ye', 'name' => 'Yemen'],
        ['code' => 'zm', 'name' => 'Zambia'],
        ['code' => 'zw', 'name' => 'Zimbabwe'],
    ];

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
            return ($t / 4294967296);
        };
    }

    private function rint(\Closure $rand, int $min, int $max): int
    {
        if ($max <= $min) return $min;
        return $min + (int) floor($rand() * (($max - $min) + 1));
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

    private function flagUrl(string $code): string
    {
        return 'https://flagcdn.com/w160/' . strtolower($code) . '.png';
    }

    private function makeQuestion(Carbon $date, int $index): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString() . '|Q|' . $index);
        $rand = $this->rng($seed);

        $countries = self::COUNTRIES;
        $total = count($countries);

        // Pick correct country
        $correctIdx = abs($seed) % $total;
        $correct = $countries[$correctIdx];

        // Pick 3 distractors (different from correct)
        $pool = array_values(array_filter($countries, fn($c) => $c['code'] !== $correct['code']));
        $pool = $this->shuffleDeterministic($pool, $rand);
        $distractors = array_slice($pool, 0, 3);

        // Build options array and shuffle
        $options = array_merge([$correct], $distractors);
        $options = $this->shuffleDeterministic($options, $rand);

        return [
            'idx'      => $index,
            'code'     => $correct['code'],
            'flag_url' => $this->flagUrl($correct['code']),
            'options'  => array_values($options),
            'answer'   => $correct['code'],
        ];
    }

    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + (abs($seed) % 900);

        return [
            'number'     => $number,
            'date'       => $date->toDateString(),
            'total'      => self::TOTAL_QUESTIONS,
            'max_wrong'  => self::MAX_WRONG,
            'penalty_ms' => self::PENALTY_MS_PER_WRONG,
        ];
    }

    private function initialState(): array
    {
        return [
            'started_ms'  => now()->getTimestampMs(),
            'current_idx' => 0,
            'wrong'       => 0,
            'answered'    => 0,
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
        $user = $request->user();
        $today = now()->startOfDay();

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
        $state['wrong']       = (int)($state['wrong'] ?? 0);
        $state['answered']    = (int)($state['answered'] ?? 0);

        $run->state = $state;
        $run->save();

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int)$run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        $isFailed = (!$run->solved && !empty($run->finished_at));

        $currentIdx = min(self::TOTAL_QUESTIONS - 1, max(0, (int)($state['current_idx'] ?? 0)));
        $question   = $this->makeQuestion($today, $currentIdx);

        return view('games.flag-guess', [
            'user'       => $user,
            'puzzleMeta' => $puzzleMeta,
            'question'   => $question,

            'run'      => $run,
            'state'    => $state,
            'isFailed' => $isFailed,

            'scope'    => $lb['scope'],
            'tabs'     => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank'   => $lb['my_rank'],

            'streak' => $streak,
        ]);
    }

    public function answer(Request $request)
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
            return back();
        }

        $state = $run->state ?: $this->initialState();
        $state['current_idx'] = (int)($state['current_idx'] ?? 0);
        $state['wrong']       = (int)($state['wrong'] ?? 0);
        $state['answered']    = (int)($state['answered'] ?? 0);
        $state['started_ms']  = (int)($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? now()->getTimestampMs()));

        // Always sync to server's idx
        $idx    = (int) $state['current_idx'];
        $choice = (string) $request->input('choice', '');

        $q       = $this->makeQuestion($today, $idx);
        $correct = ($choice === $q['answer']);

        $state['answered'] = (int) $state['answered'] + 1;
        $run->attempts     = (int) $state['answered'];

        if (!$correct) {
            $state['wrong'] = (int) $state['wrong'] + 1;
        }

        $finished = false;
        $solved   = false;
        $failed   = false;

        // Fail condition
        if ((int) $state['wrong'] >= self::MAX_WRONG) {
            $finished = true;
            $failed   = true;

            $run->finished_at = now();
            $run->duration_ms = null;
            $run->solved      = false;

            app(\App\Services\DailyGameStreakService::class)
                ->recordSolved($user, self::GAME_KEY, $today);
        } else {
            $state['current_idx'] = (int) $state['current_idx'] + 1;

            if ((int) $state['current_idx'] >= self::TOTAL_QUESTIONS) {
                $finished = true;
                $solved   = true;

                $run->solved      = true;
                $run->finished_at = now();

                $nowMs   = now()->getTimestampMs();
                $raw     = max(0, $nowMs - (int) $state['started_ms']);
                $penalty = ((int) $state['wrong']) * self::PENALTY_MS_PER_WRONG;

                $run->duration_ms = $raw + $penalty;

                // Reward logic
                $todayStr = now()->toDateString();
                if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                    $user->daily_challenges_done = 0;
                    $user->daily_challenges_date = $todayStr;
                }

                $limit    = $user->plan === 'pro' ? null : 5;
                $canReward = is_null($limit) || ((int) $user->daily_challenges_done < (int) $limit);

                if ($canReward) {
                    $user->daily_challenges_done = (int) $user->daily_challenges_done + 1;
                    $user->addXp(150);
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
            'ok'          => true,
            'correct'     => $correct,
            'correct_code' => $q['answer'],
            'current_idx' => (int)($state['current_idx'] ?? 0),
            'wrong'       => (int)($state['wrong'] ?? 0),
            'answered'    => (int)($state['answered'] ?? 0),

            'finished' => $finished,
            'solved'   => $solved,
            'failed'   => $failed,

            'final_time' => $this->mmss($run->duration_ms),
        ];

        // Next question
        if (!$finished) {
            $nextIdx            = min(self::TOTAL_QUESTIONS - 1, max(0, (int) $state['current_idx']));
            $payload['question'] = $this->makeQuestion($today, $nextIdx);
        }

        $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        if ($solved && $run->duration_ms !== null) {
            $payload['leaderboard'] = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
        }

        if ($this->wantsJson($request)) {
            return response()->json($payload);
        }

        return back();
    }
}
