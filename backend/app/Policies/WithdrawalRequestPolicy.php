<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\WithdrawalRequest;

class WithdrawalRequestPolicy
{
    public function viewRestaurant(User $user, WithdrawalRequest $withdrawal): bool
    {
        $wallet = $withdrawal->wallet;
        if (! $wallet || $wallet->holder_type !== Restaurant::class) {
            return false;
        }

        $restaurant = Restaurant::query()->find($wallet->holder_id);

        return $restaurant && (int) $restaurant->owner_id === (int) $user->id;
    }

    public function complete(User $user, WithdrawalRequest $withdrawal): bool
    {
        return $user->hasRole('admin|super-admin');
    }

    public function reject(User $user, WithdrawalRequest $withdrawal): bool
    {
        return $user->hasRole('admin|super-admin');
    }
}
