<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CreditService
{
    const SEARCH_CREDIT_COST = 1; // Cost per search

    /**
     * Check if user has enough credits for a search
     */
    public function canSearch(User $user): bool
    {
        $credits = $user->getCredits();
        return $credits->hasEnoughCredits(self::SEARCH_CREDIT_COST);
    }

    /**
     * Deduct credits for a search
     */
    public function deductSearchCredit(User $user): bool
    {
        try {
            $credits = $user->getCredits();
            return $credits->deductCredits(self::SEARCH_CREDIT_COST, 'search');
        } catch (\Exception $e) {
            Log::error('Error deducting search credit: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's current credit balance
     */
    public function getBalance(User $user): float
    {
        return $user->getCredits()->balance;
    }

    /**
     * Get credit statistics
     */
    public function getStats(User $user): array
    {
        $credits = $user->getCredits();
        return [
            'balance' => $credits->balance,
            'lifetime_purchased' => $credits->lifetime_purchased,
            'lifetime_used' => $credits->lifetime_used,
            'searches_remaining' => floor($credits->balance / self::SEARCH_CREDIT_COST),
        ];
    }

    /**
     * Give free trial credits to new user
     */
    public function giveTrialCredits(User $user, float $amount = 5): void
    {
        $credits = $user->getCredits();
        $credits->addCredits($amount, 'trial');
    }
}
