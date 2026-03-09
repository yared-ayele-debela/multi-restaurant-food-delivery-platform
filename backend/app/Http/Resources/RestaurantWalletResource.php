<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Wallet */
class RestaurantWalletResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'balance' => (string) $this->balance,
            'total_earned' => (string) $this->total_earned,
            'total_withdrawn' => (string) $this->total_withdrawn,
            'total_commission_paid' => (string) $this->total_commission_paid,
            'currency' => $this->currency,
        ];
    }
}
