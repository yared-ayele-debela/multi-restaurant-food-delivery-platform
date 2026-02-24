<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->id === $order->user_id) {
            return true;
        }

        return $this->manageRestaurantOrder($user, $order);
    }

    public function manageRestaurantOrder(User $user, Order $order): bool
    {
        if (! $user->hasPermissionTo('orders.manage_restaurant')) {
            return false;
        }

        $order->loadMissing('restaurant');

        $ownerId = $order->restaurant->owner_id;

        return $ownerId !== null && (int) $ownerId === (int) $user->id;
    }
}
