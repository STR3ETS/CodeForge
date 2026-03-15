<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GeoGuessController extends Controller
{
    private const GAME_KEY = 'geo-guess';

    /**
     * Curated list of locations with guaranteed official Google Street View coverage.
     * Each entry: [lat, lng, country_hint]
     */
    private const LOCATIONS = [
        // Europe
        [48.8566, 2.3522, 'Frankrijk'],
        [41.9028, 12.4964, 'Italië'],
        [40.4168, -3.7038, 'Spanje'],
        [52.5200, 13.4050, 'Duitsland'],
        [51.5074, -0.1278, 'Verenigd Koninkrijk'],
        [59.3293, 18.0686, 'Zweden'],
        [60.1699, 24.9384, 'Finland'],
        [55.6761, 12.5683, 'Denemarken'],
        [59.9139, 10.7522, 'Noorwegen'],
        [50.0755, 14.4378, 'Tsjechië'],
        [47.4979, 19.0402, 'Hongarije'],
        [48.2082, 16.3738, 'Oostenrijk'],
        [46.2044, 6.1432, 'Zwitserland'],
        [38.7223, -9.1393, 'Portugal'],
        [37.9838, 23.7275, 'Griekenland'],
        [44.4268, 26.1025, 'Roemenië'],
        [42.6977, 23.3219, 'Bulgarije'],
        [52.2297, 21.0122, 'Polen'],
        [50.4501, 30.5234, 'Oekraïne'],
        [56.9496, 24.1052, 'Letland'],
        [54.6872, 25.2797, 'Litouwen'],
        [59.4370, 24.7536, 'Estland'],
        [45.4642, 9.1900, 'Italië'],
        [43.2965, 5.3698, 'Frankrijk'],
        [53.3498, -6.2603, 'Ierland'],
        [41.3851, 2.1734, 'Spanje'],
        [45.0703, 7.6869, 'Italië'],
        [51.2194, 4.4025, 'België'],
        [52.3676, 4.9041, 'Nederland'],
        [47.3769, 8.5417, 'Zwitserland'],

        // North America
        [40.7128, -74.0060, 'Verenigde Staten'],
        [34.0522, -118.2437, 'Verenigde Staten'],
        [41.8781, -87.6298, 'Verenigde Staten'],
        [29.7604, -95.3698, 'Verenigde Staten'],
        [33.4484, -112.0740, 'Verenigde Staten'],
        [47.6062, -122.3321, 'Verenigde Staten'],
        [25.7617, -80.1918, 'Verenigde Staten'],
        [39.7392, -104.9903, 'Verenigde Staten'],
        [36.1699, -115.1398, 'Verenigde Staten'],
        [37.7749, -122.4194, 'Verenigde Staten'],
        [43.6532, -79.3832, 'Canada'],
        [45.5017, -73.5673, 'Canada'],
        [49.2827, -123.1207, 'Canada'],
        [51.0447, -114.0719, 'Canada'],
        [19.4326, -99.1332, 'Mexico'],
        [20.6597, -103.3496, 'Mexico'],
        [25.6866, -100.3161, 'Mexico'],

        // South America
        [-23.5505, -46.6333, 'Brazilië'],
        [-22.9068, -43.1729, 'Brazilië'],
        [-15.7975, -47.8919, 'Brazilië'],
        [-34.6037, -58.3816, 'Argentinië'],
        [-33.4489, -70.6693, 'Chili'],
        [-12.0464, -77.0428, 'Peru'],
        [4.7110, -74.0721, 'Colombia'],
        [10.4806, -66.9036, 'Venezuela'],
        [-0.1807, -78.4678, 'Ecuador'],
        [-34.9011, -56.1645, 'Uruguay'],

        // Asia
        [35.6762, 139.6503, 'Japan'],
        [34.6937, 135.5023, 'Japan'],
        [43.0618, 141.3545, 'Japan'],
        [37.5665, 126.9780, 'Zuid-Korea'],
        [35.1796, 129.0756, 'Zuid-Korea'],
        [25.0330, 121.5654, 'Taiwan'],
        [13.7563, 100.5018, 'Thailand'],
        [14.5995, 120.9842, 'Filipijnen'],
        [3.1390, 101.6869, 'Maleisië'],
        [1.3521, 103.8198, 'Singapore'],
        [-6.2088, 106.8456, 'Indonesië'],
        [-8.3405, 115.0920, 'Indonesië'],
        [21.0278, 105.8342, 'Vietnam'],
        [11.5564, 104.9282, 'Cambodja'],
        [28.6139, 77.2090, 'India'],
        [19.0760, 72.8777, 'India'],
        [12.9716, 77.5946, 'India'],
        [41.0082, 28.9784, 'Turkije'],
        [39.9334, 32.8597, 'Turkije'],
        [31.7683, 35.2137, 'Israël'],
        [32.0853, 34.7818, 'Israël'],
        [25.2048, 55.2708, 'VAE'],

        // Africa
        [-33.9249, 18.4241, 'Zuid-Afrika'],
        [-26.2041, 28.0473, 'Zuid-Afrika'],
        [-29.8587, 31.0218, 'Zuid-Afrika'],
        [6.5244, 3.3792, 'Nigeria'],
        [-1.2921, 36.8219, 'Kenia'],
        [-6.7924, 39.2083, 'Tanzania'],
        [33.5731, -7.5898, 'Marokko'],
        [36.8065, 10.1815, 'Tunesië'],
        [30.0444, 31.2357, 'Egypte'],
        [14.6928, -17.4467, 'Senegal'],
        [5.6037, -0.1870, 'Ghana'],

        // Oceania
        [-33.8688, 151.2093, 'Australië'],
        [-37.8136, 144.9631, 'Australië'],
        [-27.4698, 153.0251, 'Australië'],
        [-31.9505, 115.8605, 'Australië'],
        [-36.8485, 174.7633, 'Nieuw-Zeeland'],
        [-41.2865, 174.7762, 'Nieuw-Zeeland'],
        [-43.5321, 172.6362, 'Nieuw-Zeeland'],

        // More diverse locations
        [64.1466, -21.9426, 'IJsland'],
        [35.8989, 14.5146, 'Malta'],
        [43.7384, 7.4246, 'Monaco'],
        [42.5063, 1.5218, 'Andorra'],
        [49.6117, 6.1300, 'Luxemburg'],
        [55.7558, 37.6173, 'Rusland'],
        [39.0742, 21.8243, 'Griekenland'],
        [36.1408, -5.3536, 'Gibraltar'],
        [35.1856, 33.3823, 'Cyprus'],
        [44.8125, 20.4612, 'Servië'],
        [43.8563, 18.4131, 'Bosnië-Herzegovina'],
        [42.4304, 19.2594, 'Montenegro'],
        [46.0569, 14.5058, 'Slovenië'],
        [45.8150, 15.9819, 'Kroatië'],
        [61.5240, 105.3188, 'Rusland'],
        [22.3193, 114.1694, 'Hongkong'],
        [-22.9099, -43.2095, 'Brazilië'],
        [18.4655, -66.1057, 'Puerto Rico'],
        [21.3069, -157.8583, 'Hawaï'],
        [64.9631, -19.0208, 'IJsland'],
    ];

    private function dailyPuzzle(Carbon $date): array
    {
        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $number = 100 + ($seed % 900);
        $rng = $this->seededRng($seed);

        // Pick a random location from the curated list
        $idx = (int) floor($rng() * count(self::LOCATIONS));
        $location = self::LOCATIONS[$idx];

        // Add some offset (50-500m) so the exact city center isn't always the drop
        $latOffset = ($rng() - 0.5) * 0.008;  // ~400m range
        $lngOffset = ($rng() - 0.5) * 0.008;

        $lat = round($location[0] + $latOffset, 6);
        $lng = round($location[1] + $lngOffset, 6);

        return [
            'number' => $number,
            'date' => $date->toDateString(),
            'lat' => $lat,
            'lng' => $lng,
            'country' => $location[2],
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

    /**
     * Calculate distance between two points using Haversine formula.
     * Returns distance in meters.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000; // Earth's radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    private function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }
        return number_format($meters / 1000, 1, ',', '.') . ' km';
    }

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $distanceM): array
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

        // Sort by duration_ms ASC — we store distance_m here, so lowest = best
        $topTimes = (clone $lbQuery)
            ->with(['user:id,name,profile_picture,plan,level,xp'])
            ->orderBy('duration_ms')
            ->orderBy('finished_at')
            ->limit(10)
            ->get();

        $myRank = null;
        if ($distanceM !== null) {
            $myRank = (clone $lbQuery)
                    ->where('duration_ms', '<', $distanceM)
                    ->count() + 1;
        }

        $rows = $topTimes->map(function ($r) {
            $u = $r->user;
            $state = $r->state ?? [];
            $distM = (int) $r->duration_ms;
            return [
                'distance_m' => $distM,
                'distance' => $this->formatDistance($distM),
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

        $distanceM = $run->solved ? (int) $run->duration_ms : null;
        $lb = $this->leaderboardPayload($request, $today, $user, $distanceM);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        // Only send target location to frontend if already solved (prevent cheating)
        $puzzleForFrontend = [
            'number' => $puzzle['number'],
            'date' => $puzzle['date'],
            'lat' => $puzzle['lat'],
            'lng' => $puzzle['lng'],
        ];

        // If solved, include the target for displaying the result
        $target = $run->solved ? [
            'lat' => $puzzle['lat'],
            'lng' => $puzzle['lng'],
            'country' => $puzzle['country'],
        ] : null;

        return view('games.geo-guess', [
            'user' => $user,
            'puzzle' => $puzzleForFrontend,
            'target' => $target,
            'run' => $run,
            'state' => $state,
            'scope' => $lb['scope'],
            'tabs' => $tabs,
            'topTimes' => collect($lb['rows']),
            'myRank' => $lb['my_rank'],
            'streak' => $streak,
            'mapsApiKey' => config('services.google_maps.api_key', ''),
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
                $puzzle = $this->dailyPuzzle($today);
                $distanceM = (int) $run->duration_ms;
                $lb = $this->leaderboardPayload($request, $today, $user, $distanceM);
                $streak = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);
                $state = $run->state ?? [];

                return response()->json([
                    'ok' => true,
                    'solved' => true,
                    'distance_m' => $distanceM,
                    'distance' => $this->formatDistance($distanceM),
                    'target' => [
                        'lat' => $puzzle['lat'],
                        'lng' => $puzzle['lng'],
                        'country' => $puzzle['country'],
                    ],
                    'leaderboard' => $lb,
                    'streak' => $streak,
                ]);
            }
            return back();
        }

        $guessLat = (float) $request->input('guess_lat', 0);
        $guessLng = (float) $request->input('guess_lng', 0);
        $durationMs = max(1, (int) $request->input('duration_ms', 0));

        $puzzle = $this->dailyPuzzle($today);
        $distanceM = (int) round($this->haversineDistance(
            $puzzle['lat'], $puzzle['lng'],
            $guessLat, $guessLng
        ));

        // Store distance in meters as duration_ms for leaderboard sorting
        $run->solved = true;
        $run->finished_at = now();
        $run->duration_ms = $distanceM;
        $run->attempts = 1;
        $run->state = array_merge($run->state ?? [], [
            'guess_lat' => $guessLat,
            'guess_lng' => $guessLng,
            'target_lat' => $puzzle['lat'],
            'target_lng' => $puzzle['lng'],
            'distance_m' => $distanceM,
            'time_ms' => $durationMs,
            'country' => $puzzle['country'],
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
            $lb = $this->leaderboardPayload($request, $today, $user, $distanceM);

            return response()->json([
                'ok' => true,
                'solved' => true,
                'distance_m' => $distanceM,
                'distance' => $this->formatDistance($distanceM),
                'target' => [
                    'lat' => $puzzle['lat'],
                    'lng' => $puzzle['lng'],
                    'country' => $puzzle['country'],
                ],
                'leaderboard' => $lb,
                'streak' => $streak,
            ]);
        }

        return back();
    }
}
