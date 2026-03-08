<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class RestaurantWalletService
{
    /**
     * Credit the restaurant's wallet when an order reaches completed status (idempotent per order).
     */
    public function creditForCompletedOrder(Order $order): void
    {
        $amount = round((float) $order->restaurant_earnings, 2);
        $commission = round((float) $order->commission_amount, 2);

        if ($amount <= 0) {
            return;
        }

        $referenceType = Order::class;

        $wallet = Wallet::query()->firstOrCreate(
            [
                'holder_type' => Restaurant::class,
                'holder_id' => $order->restaurant_id,
            ],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'total_commission_paid' => 0,
                'currency' => config('food-delivery.currency', 'USD'),
                'is_active' => true,
            ]
        );

        $wallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

        if (WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $order->id)
            ->where('transaction_type', 'order_earning')
            ->exists()) {
            return;
        }

        $before = round((float) $wallet->balance, 2);
        $after = round($before + $amount, 2);

        $wallet->update([
            'balance' => $after,
            'total_earned' => round((float) $wallet->total_earned + $amount, 2),
            'total_commission_paid' => round((float) $wallet->total_commission_paid + $commission, 2),
        ]);

        WalletTransaction::query()->create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'transaction_type' => 'order_earning',
            'reference_type' => $referenceType,
            'reference_id' => $order->id,
            'description' => 'Order '.$order->order_number.' completed',
            'metadata' => [
                'commission_amount' => $commission,
            ],
            'created_at' => now(),
        ]);
    }
}
