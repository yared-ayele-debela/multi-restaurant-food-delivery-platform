<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Wallet extends Model
{
    protected $fillable = [
        'holder_type',
        'holder_id',
        'balance',
        'total_earned',
        'total_withdrawn',
        'total_commission_paid',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_earned' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
            'total_commission_paid' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'holder_type', 'holder_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
}
