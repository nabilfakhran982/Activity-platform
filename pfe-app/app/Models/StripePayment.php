<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StripePayment extends Model
{
    use HasFactory;

    protected $table = 'stripe_payments';

    protected $fillable = [
        'user_id',
        'user_credit_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'amount',
        'credits_purchased',
        'currency',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'float',
        'credits_purchased' => 'float',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userCredit()
    {
        return $this->belongsTo(UserCredit::class);
    }
}
