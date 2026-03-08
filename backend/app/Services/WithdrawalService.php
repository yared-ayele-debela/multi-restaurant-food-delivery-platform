<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalService
{
    /**
     * Restaurant owner creates a pending withdrawal (no balance change until completed by admin).
     *
     * @param  array<string, mixed>  $bank
     */
    public function requestRestaurantWithdrawal(User $user, int $restaurantId, float $amount, array $bank = []): WithdrawalRequest
    {
        $restaurant = Restaurant::query()
            ->whereKey($restaurantId)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => ['Amount must be greater than zero.'],
            ]);
        }

        return DB::transaction(function () use ($restaurant, $amount, $bank) {
            $wallet = Wallet::query()->firstOrCreate(
                [
                    'holder_type' => Restaurant::class,
                    'holder_id' => $restaurant->id,
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

            if (round((float) $wallet->balance, 2) < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient wallet balance.'],
                ]);
            }

            return WithdrawalRequest::query()->create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'bank_name' => $bank['bank_name'] ?? null,
                'account_number' => $bank['account_number'] ?? null,
                'account_holder_name' => $bank['account_holder_name'] ?? null,
                'routing_number' => $bank['routing_number'] ?? null,
                'payment_method' => $bank['payment_method'] ?? 'bank_transfer',
                'payment_details' => $bank['payment_details'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    public function completeWithdrawal(WithdrawalRequest $withdrawal, User $admin, ?string $adminNotes = null): WithdrawalRequest
    {
        return DB::transaction(function () use ($withdrawal, $admin, $adminNotes) {
            $withdrawal = WithdrawalRequest::query()->whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'withdrawal' => ['Only pending withdrawals can be completed.'],
                ]);
            }

            $amount = round((float) $withdrawal->amount, 2);

            $wallet = Wallet::query()->whereKey($withdrawal->wallet_id)->lockForUpdate()->firstOrFail();

            $before = round((float) $wallet->balance, 2);

            if ($before < $amount) {
                throw ValidationException::withMessages([
                    'withdrawal' => ['Insufficient wallet balance to complete this withdrawal.'],
                ]);
            }

            $after = round($before - $amount, 2);

            $wallet->update([
                'balance' => $after,
                'total_withdrawn' => round((float) $wallet->total_withdrawn + $amount, 2),
            ]);

            WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'transaction_type' => 'withdrawal',
                'reference_type' => WithdrawalRequest::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Withdrawal #'.$withdrawal->id,
                'metadata' => [
                    'admin_user_id' => $admin->id,
                ],
                'created_at' => now(),
            ]);

            $withdrawal->update([
                'status' => 'completed',
                'admin_notes' => $adminNotes,
                'processed_by' => $admin->id,
                'processed_at' => now(),
                'completed_at' => now(),
            ]);

            return $withdrawal->fresh();
        });
    }

    public function rejectWithdrawal(WithdrawalRequest $withdrawal, User $admin, string $reason, ?string $adminNotes = null): WithdrawalRequest
    {
        return DB::transaction(function () use ($withdrawal, $admin, $reason, $adminNotes) {
            $withdrawal = WithdrawalRequest::query()->whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'withdrawal' => ['Only pending withdrawals can be rejected.'],
                ]);
            }

            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'admin_notes' => $adminNotes,
                'processed_by' => $admin->id,
                'processed_at' => now(),
                'rejected_at' => now(),
            ]);

            return $withdrawal->fresh();
        });
    }
}
