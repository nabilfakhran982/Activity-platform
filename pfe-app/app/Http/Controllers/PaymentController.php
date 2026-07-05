<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected StripeService $stripeService;
    protected CreditService $creditService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->stripeService = new StripeService();
        $this->creditService = new CreditService();
    }

    /**
     * Show credit packages
     */
    public function showPackages()
    {
        $packages = array_values($this->stripeService->getCreditPackages());
        $userStats = $this->creditService->getStats(Auth::user());

        return view('payment.packages', compact('packages', 'userStats'));
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|between:1,4',
        ]);

        $result = $this->stripeService->createPaymentIntent(
            Auth::user(),
            $request->integer('package_id')
        );

        return response()->json($result);
    }

    /**
     * Confirm payment and add credits
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $result = $this->stripeService->confirmPayment($request->string('payment_intent_id'));

        return response()->json($result);
    }

    /**
     * Handle Stripe webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
            $this->stripeService->handleWebhookEvent($event->toArray());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
