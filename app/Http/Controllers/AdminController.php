<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Revenue stats
        $totalUsers      = User::count();
        $proUsers        = User::where('plan', 'pro')->count();
        $freeUsers       = $totalUsers - $proUsers;
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();

        // Monthly Recurring Revenue estimate
        $monthlyCount = Subscription::where('stripe_status', 'active')
            ->whereHas('items', function ($q) {
                $q->where('stripe_price', config('services.stripe.price_monthly'));
            })->count();

        $yearlyCount = Subscription::where('stripe_status', 'active')
            ->whereHas('items', function ($q) {
                $q->where('stripe_price', config('services.stripe.price_yearly'));
            })->count();

        // MRR: monthly subs * 3.49 + yearly subs * (23.88/12 = 1.99)
        $mrr = ($monthlyCount * 3.49) + ($yearlyCount * 1.99);

        // Recent users with subscription info
        $users = User::orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($user) {
                $sub = $user->subscription('pro');
                return [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'plan'            => $user->plan ?? 'free',
                    'is_admin'        => $user->is_admin,
                    'stripe_id'       => $user->stripe_id,
                    'sub_status'      => $sub?->stripe_status,
                    'sub_ends_at'     => $sub?->ends_at?->format('d-m-Y'),
                    'sub_created'     => $sub?->created_at?->format('d-m-Y'),
                    'created_at'      => $user->created_at->format('d-m-Y'),
                ];
            });

        // Cancelled subscriptions (churned)
        $cancelledSubs = Subscription::whereNotNull('ends_at')
            ->orderByDesc('ends_at')
            ->limit(50)
            ->get()
            ->map(function ($sub) {
                $user = User::find($sub->user_id);
                return [
                    'user_name'  => $user?->name ?? 'Verwijderd',
                    'user_email' => $user?->email ?? '-',
                    'status'     => $sub->stripe_status,
                    'ends_at'    => $sub->ends_at?->format('d-m-Y'),
                    'created_at' => $sub->created_at->format('d-m-Y'),
                ];
            });

        return view('admin.dashboard', compact(
            'totalUsers', 'proUsers', 'freeUsers',
            'activeSubscriptions', 'mrr',
            'monthlyCount', 'yearlyCount',
            'users', 'cancelledSubs'
        ));
    }

    /**
     * Toggle admin status for a user.
     */
    public function toggleAdmin(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Je kunt je eigen admin-status niet wijzigen.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return back()->with('flash', "{$user->name} is nu " . ($user->is_admin ? 'admin' : 'geen admin meer') . '.');
    }

    /**
     * Force-downgrade a user to free.
     */
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
