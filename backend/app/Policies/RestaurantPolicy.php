<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Models\User;

class RestaurantPolicy
{
    /**
     * Check if user can manage the restaurant (owner or permitted staff)
     */
    public function manage(User $user, Restaurant $restaurant): bool
    {
        // Restaurant owner
        if ($restaurant->owner_id === $user->id) {
            return true;
        }

        // User with restaurant.manage permission
        if ($user->hasPermissionTo('restaurant.manage')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view the restaurant dashboard
     */
    public function viewDashboard(User $user, Restaurant $restaurant): bool
    {
        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can manage menu (categories, products, etc.)
     */
    public function manageMenu(User $user, Restaurant $restaurant): bool
    {
        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can manage orders
     */
    public function manageOrders(User $user, Restaurant $restaurant): bool
    {
        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can manage branches
     */
    public function manageBranches(User $user, Restaurant $restaurant): bool
    {
        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can manage operating hours
     */
    public function manageHours(User $user, Restaurant $restaurant): bool
    {
        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can update a specific order
     */
    public function updateOrder(User $user, Order $order): bool
    {
        $restaurant = $order->restaurant;

        if (!$restaurant) {
            return false;
        }

        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user can update a specific branch
     */
    public function updateBranch(User $user, RestaurantBranch $branch): bool
    {
        $restaurant = $branch->restaurant;

        if (!$restaurant) {
            return false;
        }

        return $this->manage($user, $restaurant);
    }

    /**
     * Check if user has restaurant owner permission
     */
    public function isOwner(User $user): bool
    {
        return $user->hasPermissionTo('restaurant.owner') || $user->hasPermissionTo('restaurant.manage');
    }
}
