<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditTransaction extends Model
{
    use HasFactory;

    protected $table = 'credit_transactions';

    protected $fillable = [
        'user_credit_id',
        'type',
        'amount',
        'reason',
        'balance_after',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function userCredit()
    {
        return $this->belongsTo(UserCredit::class);
    }

    public function user()
    {
        return $this->userCredit->user();
    }
}
