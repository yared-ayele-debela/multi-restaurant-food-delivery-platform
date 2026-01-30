<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 15, 120);
        $deliveryFee = fake()->randomFloat(2, 2, 5);
        $taxRate = 0.15;
        $taxAmount = round($subtotal * $taxRate, 2);
        $total = $subtotal + $deliveryFee + $taxAmount;
        $commissionRate = 0.10;

        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::factory(),
            'branch_id' => null, // Will be set based on restaurant
            'address_id' => UserAddress::factory(),
            'coupon_id' => null,
            'status' => fake()->randomElement(OrderStatus::cases())->value,
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'delivery_fee' => $deliveryFee,
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxRate,
            'total' => $total,
            'commission_rate' => $commissionRate,
            'commission_amount' => round($total * $commissionRate, 2),
            'restaurant_earnings' => round($subtotal * 0.85, 2),
            'driver_earnings' => round($deliveryFee * 0.7, 2),
            'payment_method' => fake()->randomElement(['cash', 'card', 'wallet']),
            'payment_status' => fake()->randomElement(['pending', 'paid']),
            'stripe_payment_intent_id' => null,
            'delivery_address' => null,
            'delivery_notes' => fake()->optional(30)->sentence(),
            'placed_at' => now()->subMinutes(fake()->numberBetween(5, 120)),
            'accepted_at' => null,
            'preparing_at' => null,
            'ready_at' => null,
            'picked_up_at' => null,
            'delivered_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'cancelled_by' => null,
            'loyalty_points_earned' => floor($total / 10),
            'loyalty_points_redeemed' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Order $order) {
            // Set branch based on restaurant
            if (!$order->branch_id && $order->restaurant_id) {
                $branch = RestaurantBranch::where('restaurant_id', $order->restaurant_id)->first();
                if ($branch) {
                    $order->branch_id = $branch->id;
                }
            }

            // Set delivery address from user's address
            if (!$order->delivery_address && $order->address_id) {
                $address = UserAddress::find($order->address_id);
                if ($address) {
                    $order->delivery_address = [
                        'address_line' => $address->address_line_1,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => $address->postal_code,
                        'country' => $address->country,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                    ];
                }
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Pending->value,
            'accepted_at' => null,
            'preparing_at' => null,
            'ready_at' => null,
            'picked_up_at' => null,
            'delivered_at' => null,
            'completed_at' => null,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Accepted->value,
            'accepted_at' => now()->subMinutes(fake()->numberBetween(1, 10)),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Completed->value,
            'accepted_at' => now()->subHours(2)->subMinutes(30),
            'preparing_at' => now()->subHours(2)->subMinutes(20),
            'ready_at' => now()->subHours(2)->subMinutes(5),
            'picked_up_at' => now()->subHours(2),
            'delivered_at' => now()->subHours(1)->subMinutes(30),
            'completed_at' => now()->subHours(1)->subMinutes(15),
        ]);
    }
}
