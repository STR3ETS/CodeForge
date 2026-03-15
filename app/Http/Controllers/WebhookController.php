<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;

class WebhookController extends CashierWebhookController
{
    public function handleWebhook(Request $request)
    {
        if (empty(config('cashier.webhook.secret'))) {
            return response('Webhook secret not configured, skipping.', 200);
        }

        return parent::handleWebhook($request);
    }

    /**
     * Handle customer subscription created.
     */
    public function handleCustomerSubscriptionCreated(array $payload): void
    {
        parent::handleCustomerSubscriptionCreated($payload);
        $this->syncPlanFromPayload($payload, 'pro');
    }

    /**
     * Handle customer subscription updated (renewal, plan change, etc.).
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

        $status = $payload['data']['object']['status'] ?? '';

        if (in_array($status, ['active', 'trialing'])) {
            $this->syncPlanFromPayload($payload, 'pro');
        } elseif (in_array($status, ['canceled', 'unpaid', 'past_due', 'incomplete_expired'])) {
            $this->syncPlanFromPayload($payload, 'free');
        }
    }

    /**
     * Handle customer subscription deleted (cancellation / end of period).
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);
        $this->syncPlanFromPayload($payload, 'free');
    }

    /**
     * Handle charge refunded — auto-downgrade.
     */
    public function handleChargeRefunded(array $payload): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                $user->update(['plan' => 'free']);

                // Cancel the subscription in Stripe too
                $sub = $user->subscription('pro');
                if ($sub && !$sub->cancelled()) {
                    $sub->cancelNow();
                }

                Log::info("User #{$user->id} downgraded due to refund.", [
                    'charge_id' => $payload['data']['object']['id'] ?? null,
                ]);
            }
        }
    }

    /**
     * Handle dispute (chargeback) created — auto-downgrade.
     */
    public function handleChargeDisputeCreated(array $payload): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                $user->update(['plan' => 'free']);

                $sub = $user->subscription('pro');
                if ($sub && !$sub->cancelled()) {
                    $sub->cancelNow();
                }

                Log::warning("User #{$user->id} downgraded due to chargeback/dispute.", [
                    'dispute_id' => $payload['data']['object']['id'] ?? null,
                ]);
            }
        }
    }

    /**
     * Sync the user's plan column from a webhook payload.
     */
    protected function syncPlanFromPayload(array $payload, string $plan): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            User::where('stripe_id', $stripeCustomerId)
                ->update(['plan' => $plan]);
        }
    }
}
