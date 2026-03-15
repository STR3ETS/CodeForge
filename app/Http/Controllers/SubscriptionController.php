<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class SubscriptionController extends Controller
{
    /**
     * Redirect to Stripe Checkout for a new subscription.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ]);

        $user = $request->user();

        // Ensure the user has a Stripe Customer ID before checkout
        if (! $user->stripe_id) {
            $user->createAsStripeCustomer([
                'name'  => $user->name,
                'email' => $user->email,
            ]);
        }

        $priceId = $request->plan === 'yearly'
            ? config('services.stripe.price_yearly')
            : config('services.stripe.price_monthly');

        return $user
            ->newSubscription('pro', $priceId)
            ->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('pages.pricing'),
            ]);
    }

    /**
     * After successful checkout, sync plan and show success.
     */
    public function success(Request $request)
    {
        $user = $request->user();
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            // Retrieve the Checkout Session from Stripe to get subscription info
            $stripe = Cashier::stripe();
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription'],
            ]);

            // Sync stripe_id if not yet set
            if (! $user->stripe_id && $session->customer) {
                $user->update(['stripe_id' => $session->customer]);
            }

            // Create subscription record in our DB if it doesn't exist yet
            if ($session->subscription) {
                $sub = $session->subscription;
                $existingSub = $user->subscription('pro');

                if (! $existingSub) {
                    $user->subscriptions()->create([
                        'type'          => 'pro',
                        'stripe_id'     => is_string($sub) ? $sub : $sub->id,
                        'stripe_status' => is_string($sub) ? 'active' : $sub->status,
                        'stripe_price'  => is_string($sub) ? null : ($sub->items->data[0]->price->id ?? null),
                        'quantity'      => 1,
                    ]);
                }
            }
        }

        // Refresh and sync plan
        $user->refresh();
        if ($user->subscribed('pro') || $user->stripe_id) {
            $user->update(['plan' => 'pro']);
        }

        return redirect()->route('dashboard')->with('pro_activated', true);
    }

    /**
     * Redirect to Stripe Billing Portal.
     */
    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }

    /**
     * Show current subscription status (used on profile/settings).
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'plan'       => $user->plan,
            'subscribed' => $user->subscribed('pro'),
            'on_trial'   => $user->onTrial('pro'),
            'cancelled'  => $user->subscription('pro')?->canceled() ?? false,
            'ends_at'    => $user->subscription('pro')?->ends_at,
        ]);
    }
}
