<?php

namespace App\Services;

use App\Models\StripePayment;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe payment intent for credit purchase
     */
    public function createPaymentIntent(User $user, int $creditPackageId): array
    {
        try {
            // Define credit packages (can be moved to database later)
            $packages = $this->getCreditPackages();
            $package = $packages[$creditPackageId] ?? null;

            if (!$package) {
                return ['success' => false, 'message' => 'Invalid credit package'];
            }

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $package['amount_cents'], // Stripe expects amount in cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $user->id,
                    'credits' => $package['credits'],
                    'package_id' => $creditPackageId,
                ],
                'description' => $user->email . ' - ' . $package['credits'] . ' Credits',
            ]);

            // Store payment record
            $payment = StripePayment::create([
                'user_id' => $user->id,
                'user_credit_id' => $user->getCredits()->id,
                'stripe_payment_intent_id' => $intent->id,
                'amount' => $package['amount'],
                'credits_purchased' => $package['credits'],
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'amount' => $package['amount'],
                'credits' => $package['credits'],
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment processing error'];
        }
    }

    /**
     * Confirm payment and add credits to user
     */
    public function confirmPayment(string $paymentIntentId): array
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return ['success' => false, 'message' => 'Payment not succeeded'];
            }

            // Find payment record
            $payment = StripePayment::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if (!$payment) {
                return ['success' => false, 'message' => 'Payment record not found'];
            }

            // Update payment status
            $chargeId = null;
            if (!empty($intent->latest_charge)) {
                $chargeId = $intent->latest_charge;
            } elseif (!empty($intent->charges->data) && count($intent->charges->data) > 0) {
                $chargeId = $intent->charges->data[0]->id;
            }

            $payment->update([
                'status' => 'succeeded',
                'stripe_charge_id' => $chargeId,
            ]);

            // Add credits to user
            $userCredits = $payment->userCredit;
            $userCredits->addCredits(
                $payment->credits_purchased,
                'purchase:stripe:' . $paymentIntentId
            );

            return [
                'success' => true,
                'credits_added' => $payment->credits_purchased,
                'new_balance' => $userCredits->balance,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe confirmation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment confirmation failed'];
        }
    }

    /**
     * Handle Stripe webhook event
     */
    public function handleWebhookEvent(array $event): bool
    {
        try {
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentSucceeded($event['data']['object']);
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentFailed($event['data']['object']);
                case 'charge.refunded':
                    return $this->handleChargeRefunded($event['data']['object']);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Webhook handling error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get predefined credit packages
     */
    public function getCreditPackages(): array
    {
        return [
            1 => ['id' => 1, 'credits' => 10, 'amount' => 4.99, 'amount_cents' => 499],
            2 => ['id' => 2, 'credits' => 25, 'amount' => 9.99, 'amount_cents' => 999],
            3 => ['id' => 3, 'credits' => 50, 'amount' => 19.99, 'amount_cents' => 1999],
            4 => ['id' => 4, 'credits' => 100, 'amount' => 39.99, 'amount_cents' => 3999],
        ];
    }

    private function handlePaymentSucceeded(object $paymentIntent): bool
    {
        $payment = StripePayment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if (!$payment || $payment->status === 'succeeded') {
            return true;
        }

        // Extract charge ID from payment intent
        $chargeId = null;
        if (!empty($paymentIntent->latest_charge)) {
            $chargeId = $paymentIntent->latest_charge;
        } elseif (!empty($paymentIntent->charges->data) && count($paymentIntent->charges->data) > 0) {
            $chargeId = $paymentIntent->charges->data[0]->id;
        }

        $payment->update([
            'status' => 'succeeded',
            'stripe_charge_id' => $chargeId,
        ]);
        $payment->userCredit->addCredits($payment->credits_purchased, 'purchase:stripe:' . $paymentIntent->id);
        return true;
    }

    private function handlePaymentFailed(object $paymentIntent): bool
    {
        $payment = StripePayment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if (!$payment) {
            return true;
        }

        $payment->update(['status' => 'failed']);
        return true;
    }

    private function handleChargeRefunded(object $charge): bool
    {
        $payment = StripePayment::where('stripe_charge_id', $charge->id)->first();
        if (!$payment) {
            return true;
        }

        // Refund the credits
        $payment->userCredit->deductCredits($payment->credits_purchased, 'refund:stripe');
        $payment->update(['status' => 'cancelled']);
        return true;
    }
}
