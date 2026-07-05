<?php

namespace App\Http\Controllers;

use App\Services\CreditService;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    protected CreditService $creditService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->creditService = new CreditService();
    }

    /**
     * Get user credit stats via API
     */
    public function getStats()
    {
        $stats = $this->creditService->getStats(Auth::user());

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Show credit dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = $this->creditService->getStats($user);
        $transactions = $user->getCredits()->transactions()->latest()->paginate(20);

        return view('credits.dashboard', compact('stats', 'transactions'));
    }

    /**
     * Show credit purchase page
     */
    public function purchase()
    {
        $packages = (new \App\Services\StripeService())->getCreditPackages();
        $userStats = $this->creditService->getStats(Auth::user());

        return view('credits.purchase', compact('packages', 'userStats'));
    }
}
