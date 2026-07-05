<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCredit extends Model
{
    use HasFactory;

    protected $table = 'user_credits';

    protected $fillable = [
        'user_id',
        'balance',
        'lifetime_purchased',
        'lifetime_used',
    ];

    protected $casts = [
        'balance' => 'float',
        'lifetime_purchased' => 'float',
        'lifetime_used' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Check if user has enough credits for a search
     */
    public function hasEnoughCredits(float $amount = 1): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Deduct credits from balance
     */
    public function deductCredits(float $amount, string $reason = 'search'): bool
    {
        if (!$this->hasEnoughCredits($amount)) {
            return false;
        }

        $this->balance -= $amount;
        $this->lifetime_used += $amount;
        $this->save();

        // Log transaction
        $this->transactions()->create([
            'type' => 'deduction',
            'amount' => $amount,
            'reason' => $reason,
            'balance_after' => $this->balance,
        ]);

        return true;
    }

    /**
     * Add credits to balance
     */
    public function addCredits(float $amount, string $reason = 'purchase'): void
    {
        $this->balance += $amount;
        $this->lifetime_purchased += $amount;
        $this->save();

        // Log transaction
        $this->transactions()->create([
            'type' => 'addition',
            'amount' => $amount,
            'reason' => $reason,
            'balance_after' => $this->balance,
        ]);
    }
}
