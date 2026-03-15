<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DailyGameRun;
use App\Models\DailyGameStreak;
use App\Models\ScorePost;
use App\Models\ChatMessage;
use App\Models\Friendship;
use App\Models\ShopItem;
use App\Models\DailyQuestClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = now();
        $today = $now->toDateString();
        $weekAgo = $now->copy()->subDays(7)->toDateString();
        $monthAgo = $now->copy()->subDays(30)->toDateString();

        // ── User stats ──
        $totalUsers = User::count();
        $proUsers = User::where('plan', 'pro')->count();
        $freeUsers = $totalUsers - $proUsers;
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersWeek = User::whereDate('created_at', '>=', $weekAgo)->count();
        $newUsersMonth = User::whereDate('created_at', '>=', $monthAgo)->count();
        $adminCount = User::where('is_admin', true)->count();

        // ── Subscription / Revenue ──
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();

        $monthlyCount = Subscription::where('stripe_status', 'active')
            ->whereHas('items', fn($q) => $q->where('stripe_price', config('services.stripe.price_monthly')))
            ->count();

        $yearlyCount = Subscription::where('stripe_status', 'active')
            ->whereHas('items', fn($q) => $q->where('stripe_price', config('services.stripe.price_yearly')))
            ->count();

        $mrr = ($monthlyCount * 3.49) + ($yearlyCount * 1.99);
        $arr = $mrr * 12;

        $cancelledSubs = Subscription::whereNotNull('ends_at')
            ->orderByDesc('ends_at')->limit(50)->get()
            ->map(function ($sub) {
                $user = User::find($sub->user_id);
                return [
                    'user_name' => $user?->name ?? 'Verwijderd',
                    'user_email' => $user?->email ?? '-',
                    'status' => $sub->stripe_status,
                    'ends_at' => $sub->ends_at?->format('d-m-Y'),
                    'created_at' => $sub->created_at->format('d-m-Y'),
                ];
            });

        // ── Game stats ──
        $totalGamesPlayed = DailyGameRun::count();
        $gamesToday = DailyGameRun::whereDate('puzzle_date', $today)->count();
        $gamesWeek = DailyGameRun::whereDate('puzzle_date', '>=', $weekAgo)->count();
        $solvedToday = DailyGameRun::whereDate('puzzle_date', $today)->where('solved', true)->count();
        $solvedRate = $gamesToday > 0 ? round(($solvedToday / $gamesToday) * 100) : 0;

        // Games per type
        $gamesByType = DailyGameRun::select('game_key', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN solved = 1 THEN 1 ELSE 0 END) as solved_count'))
            ->groupBy('game_key')
            ->orderByDesc('total')
            ->get();

        // Active players today (unique users who played)
        $activePlayersToday = DailyGameRun::whereDate('puzzle_date', $today)->distinct('user_id')->count('user_id');
        $activePlayersWeek = DailyGameRun::whereDate('puzzle_date', '>=', $weekAgo)->distinct('user_id')->count('user_id');

        // Average game duration (solved only, in seconds)
        $avgDuration = DailyGameRun::where('solved', true)->whereNotNull('duration_ms')->avg('duration_ms');
        $avgDurationFormatted = $avgDuration ? gmdate('i:s', (int)($avgDuration / 1000)) : '—';

        // ── Streak stats ──
        $topStreaks = DailyGameStreak::select('user_id', DB::raw('MAX(best_streak) as best'))
            ->groupBy('user_id')
            ->orderByDesc('best')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $user = User::find($row->user_id);
                return ['name' => $user?->name ?? 'Verwijderd', 'streak' => $row->best];
            });

        // ── Social stats ──
        $totalFriendships = Friendship::where('status', 'accepted')->count();
        $pendingRequests = Friendship::where('status', 'pending')->count();
        $totalMessages = ChatMessage::count();
        $messagesToday = ChatMessage::whereDate('created_at', $today)->count();
        $totalScorePosts = ScorePost::count();
        $scorePostsToday = ScorePost::whereDate('created_at', $today)->count();

        // ── Shop stats ──
        $totalShopItems = ShopItem::where('active', true)->count();
        $totalCoinsCirculation = (int) User::sum('coins');
        $totalItemsBought = DB::table('user_cosmetics')->count();
        $topBoughtItems = DB::table('user_cosmetics')
            ->select('shop_item_id', DB::raw('COUNT(*) as bought'))
            ->groupBy('shop_item_id')
            ->orderByDesc('bought')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $item = ShopItem::find($row->shop_item_id);
                return [
                    'name' => $item?->name ?? 'Verwijderd',
                    'type' => $item?->type ?? '-',
                    'rarity' => $item?->rarity ?? '-',
                    'bought' => $row->bought,
                ];
            });

        // ── Quest stats ──
        $questsClaimed = DailyQuestClaim::count();
        $questsToday = DailyQuestClaim::whereDate('quest_date', $today)->count();
        $totalXpRewarded = (int) DailyQuestClaim::sum('reward_xp');

        // ── Users table ──
        $users = User::orderByDesc('created_at')->limit(100)->get()
            ->map(function ($user) {
                $sub = $user->subscription('pro');
                $gamesPlayed = DailyGameRun::where('user_id', $user->id)->count();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'plan' => $user->plan ?? 'free',
                    'is_admin' => $user->is_admin,
                    'level' => (int) $user->level,
                    'xp' => (int) $user->xp,
                    'coins' => (int) $user->coins,
                    'games_played' => $gamesPlayed,
                    'sub_status' => $sub?->stripe_status,
                    'sub_ends_at' => $sub?->ends_at?->format('d-m-Y'),
                    'created_at' => $user->created_at->format('d-m-Y'),
                    'last_active' => $user->updated_at->diffForHumans(short: true),
                ];
            });

        // ── Daily registrations (last 14 days chart data) ──
        $registrationChart = collect(range(13, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'count' => User::whereDate('created_at', $date->toDateString())->count(),
            ];
        });

        // ── Daily games played (last 14 days chart data) ──
        $gamesChart = collect(range(13, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'count' => DailyGameRun::whereDate('puzzle_date', $date->toDateString())->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'totalUsers', 'proUsers', 'freeUsers', 'newUsersToday', 'newUsersWeek', 'newUsersMonth', 'adminCount',
            'activeSubscriptions', 'mrr', 'arr', 'monthlyCount', 'yearlyCount', 'cancelledSubs',
            'totalGamesPlayed', 'gamesToday', 'gamesWeek', 'solvedRate', 'gamesByType',
            'activePlayersToday', 'activePlayersWeek', 'avgDurationFormatted',
            'topStreaks',
            'totalFriendships', 'pendingRequests', 'totalMessages', 'messagesToday',
            'totalScorePosts', 'scorePostsToday',
            'totalShopItems', 'totalCoinsCirculation', 'totalItemsBought', 'topBoughtItems',
            'questsClaimed', 'questsToday', 'totalXpRewarded',
            'users', 'registrationChart', 'gamesChart'
        ));
    }

    public function toggleAdmin(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Je kunt je eigen admin-status niet wijzigen.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return back()->with('flash', "{$user->name} is nu " . ($user->is_admin ? 'admin' : 'geen admin meer') . '.');
    }

    public function downgradeUser(Request $request, User $user)
    {
        $user->update(['plan' => 'free']);

        $sub = $user->subscription('pro');
        if ($sub && !$sub->cancelled()) {
            $sub->cancelNow();
        }

        return back()->with('flash', "{$user->name} is gedowngraded naar Free.");
    }
}
