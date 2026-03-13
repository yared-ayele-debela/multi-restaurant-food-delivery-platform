<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Coupon;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $restaurants = Restaurant::all();
        $customers = User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))->get();
        $drivers = Driver::where('is_active', true)->where('is_approved', true)->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Skipping order creation.');
            return;
        }

        foreach ($restaurants as $restaurant) {
            // Create 5-15 orders per restaurant
            $orderCount = fake()->numberBetween(5, 15);

            for ($i = 0; $i < $orderCount; $i++) {
                $customer = $customers->random();
                
                // Get customer's address
                $address = $customer->addresses()->first();
                if (!$address) {
                    continue;
                }

                // Determine order status with weighted distribution
                $status = fake()->randomElement([
                    OrderStatus::Pending->value,
                    OrderStatus::Pending->value,
                    OrderStatus::Accepted->value,
                    OrderStatus::Accepted->value,
                    OrderStatus::Preparing->value,
                    OrderStatus::Ready->value,
                    OrderStatus::Completed->value,
                    OrderStatus::Completed->value,
                    OrderStatus::Completed->value,
                    OrderStatus::Cancelled->value,
                ]);

                // Generate timestamps based on status
                $timestamps = $this->generateTimestamps($status);

                // Calculate financial values
                $subtotal = fake()->randomFloat(2, 20, 150);
                $deliveryFee = $restaurant->delivery_fee ?? fake()->randomFloat(2, 2, 5);
                $taxRate = 0.15;
                $taxAmount = round($subtotal * $taxRate, 2);
                
                // Apply coupon if available (20% chance)
                $discountAmount = 0;
                $coupon = null;
                if (fake()->boolean(20)) {
                    $coupon = Coupon::where('restaurant_id', $restaurant->id)
                        ->where('is_active', true)
                        ->inRandomOrder()
                        ->first();
                    if ($coupon) {
                        if ($coupon->type === 'percentage') {
                            $discountAmount = round($subtotal * ($coupon->value / 100), 2);
                            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                                $discountAmount = $coupon->max_discount_amount;
                            }
                        } else {
                            $discountAmount = min($coupon->value, $subtotal);
                        }
                    }
                }

                $total = max(0, $subtotal - $discountAmount + $deliveryFee + $taxAmount);
                $commissionRate = $restaurant->commission_rate / 100 ?? 0.10;

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid() . fake()->bothify('###')),
                    'user_id' => $customer->id,
                    'restaurant_id' => $restaurant->id,
                    'branch_id' => $restaurant->branches()->first()?->id,
                    'address_id' => $address->id,
                    'coupon_id' => $coupon?->id,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'delivery_fee' => $deliveryFee,
                    'tax_amount' => $taxAmount,
                    'tax_rate' => $taxRate,
                    'total' => $total,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => round($subtotal * $commissionRate, 2),
                    'restaurant_earnings' => round($subtotal * (1 - $commissionRate), 2),
                    'driver_earnings' => round($deliveryFee * 0.7, 2),
                    'payment_method' => fake()->randomElement(['cash', 'card', 'wallet']),
                    'payment_status' => in_array($status, [OrderStatus::Completed->value, OrderStatus::Delivered->value]) ? 'paid' : 'pending',
                    'stripe_payment_intent_id' => null,
                    'delivery_address' => [
                        'address_line' => $address->address_line_1,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => $address->postal_code,
                        'country' => $address->country,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                    ],
                    'delivery_notes' => fake()->optional(30)->sentence(),
                    'placed_at' => $timestamps['placed_at'],
                    'accepted_at' => $timestamps['accepted_at'],
                    'preparing_at' => $timestamps['preparing_at'],
                    'ready_at' => $timestamps['ready_at'],
                    'picked_up_at' => $timestamps['picked_up_at'],
                    'delivered_at' => $timestamps['delivered_at'],
                    'completed_at' => $timestamps['completed_at'],
                    'cancelled_at' => $timestamps['cancelled_at'],
                    'cancellation_reason' => $timestamps['cancellation_reason'],
                    'cancelled_by' => $timestamps['cancelled_by'],
                    'loyalty_points_earned' => floor($total / 10),
                    'loyalty_points_redeemed' => 0,
                ]);

                // Create order items
                $this->createOrderItems($order, $restaurant);

                // Create delivery for orders that are past ready status
                if (in_array($status, [OrderStatus::Ready->value, OrderStatus::PickedUp->value, OrderStatus::OnTheWay->value, OrderStatus::Delivered->value, OrderStatus::Completed->value])) {
                    if ($drivers->isNotEmpty()) {
                        $driver = $drivers->random();
                        Delivery::create([
                            'order_id' => $order->id,
                            'driver_id' => $driver->id,
                            'pickup_latitude' => $restaurant->latitude ?? $restaurant->branches()->first()?->latitude ?? fake()->latitude(8, 10),
                            'pickup_longitude' => $restaurant->longitude ?? $restaurant->branches()->first()?->longitude ?? fake()->longitude(38, 40),
                            'dropoff_latitude' => $address->latitude,
                            'dropoff_longitude' => $address->longitude,
                            'distance_km' => fake()->randomFloat(2, 1, 15),
                            'estimated_time_minutes' => fake()->numberBetween(15, 60),
                            'actual_time_minutes' => fake()->numberBetween(10, 90),
                            'delivery_fee' => $deliveryFee,
                            'driver_earning' => round($deliveryFee * 0.7, 2),
                            'tip_amount' => fake()->boolean(30) ? fake()->randomFloat(2, 1, 10) : 0,
                            'status' => $this->mapOrderStatusToDeliveryStatus($status),
                            'assigned_at' => $timestamps['accepted_at'] ?? $timestamps['placed_at'],
                            'accepted_at' => $timestamps['accepted_at'],
                            'picked_up_at' => $timestamps['picked_up_at'],
                            'delivered_at' => $timestamps['delivered_at'],
                            'failed_reason' => null,
                            'driver_rating' => fake()->boolean(70) ? fake()->numberBetween(3, 5) : null,
                            'customer_feedback' => fake()->optional(50)->sentence(),
                        ]);
                    }
                }
            }
        }
    }

    private function createOrderItems(Order $order, Restaurant $restaurant): void
    {
        $products = Product::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();
        
        if ($products->isEmpty()) {
            return;
        }

        // Create 1-5 items per order
        $itemCount = fake()->numberBetween(1, 5);
        $selectedProducts = $products->random(min($itemCount, $products->count()));

        foreach ($selectedProducts as $product) {
            $size = ProductSize::where('product_id', $product->id)->inRandomOrder()->first();
            $unitPrice = $size ? $size->price : $product->base_price;
            $quantity = fake()->numberBetween(1, 3);

            // Add addons
            $addons = [];
            $addonsTotal = 0;
            if (fake()->boolean(40)) {
                $addonNames = ['Extra Cheese', 'Bacon', 'Mushrooms', 'Avocado', 'Spicy Sauce'];
                $selectedAddons = fake()->randomElements($addonNames, fake()->numberBetween(1, 2));
                foreach ($selectedAddons as $addonName) {
                    $price = fake()->randomFloat(2, 0.5, 3);
                    $addons[] = ['name' => $addonName, 'price' => $price];
                    $addonsTotal += $price;
                }
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_size_id' => $size?->id,
                'product_name' => $product->name,
                'product_size_name' => $size?->name,
                'item_name' => $size ? $product->name . ' (' . $size->name . ')' : $product->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'addons' => $addons,
                'addons_total' => $addonsTotal,
                'subtotal' => ($unitPrice * $quantity) + $addonsTotal,
            ]);
        }
    }

    private function generateTimestamps(string $status): array
    {
        $now = now();
        $placedAt = $now->copy()->subHours(fake()->numberBetween(1, 48));
        
        $timestamps = [
            'placed_at' => $placedAt,
            'accepted_at' => null,
            'preparing_at' => null,
            'ready_at' => null,
            'picked_up_at' => null,
            'delivered_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'cancelled_by' => null,
        ];

        switch ($status) {
            case OrderStatus::Accepted->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                break;
            case OrderStatus::Preparing->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                $timestamps['preparing_at'] = $timestamps['accepted_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                break;
            case OrderStatus::Ready->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                $timestamps['preparing_at'] = $timestamps['accepted_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['ready_at'] = $timestamps['preparing_at']->copy()->addMinutes(fake()->numberBetween(10, 30));
                break;
            case OrderStatus::PickedUp->value:
            case OrderStatus::OnTheWay->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                $timestamps['preparing_at'] = $timestamps['accepted_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['ready_at'] = $timestamps['preparing_at']->copy()->addMinutes(fake()->numberBetween(10, 30));
                $timestamps['picked_up_at'] = $timestamps['ready_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                break;
            case OrderStatus::Delivered->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                $timestamps['preparing_at'] = $timestamps['accepted_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['ready_at'] = $timestamps['preparing_at']->copy()->addMinutes(fake()->numberBetween(10, 30));
                $timestamps['picked_up_at'] = $timestamps['ready_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['delivered_at'] = $timestamps['picked_up_at']->copy()->addMinutes(fake()->numberBetween(15, 45));
                break;
            case OrderStatus::Completed->value:
                $timestamps['accepted_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 10));
                $timestamps['preparing_at'] = $timestamps['accepted_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['ready_at'] = $timestamps['preparing_at']->copy()->addMinutes(fake()->numberBetween(10, 30));
                $timestamps['picked_up_at'] = $timestamps['ready_at']->copy()->addMinutes(fake()->numberBetween(5, 15));
                $timestamps['delivered_at'] = $timestamps['picked_up_at']->copy()->addMinutes(fake()->numberBetween(15, 45));
                $timestamps['completed_at'] = $timestamps['delivered_at']->copy()->addMinutes(fake()->numberBetween(1, 10));
                break;
            case OrderStatus::Cancelled->value:
                $timestamps['cancelled_at'] = $placedAt->copy()->addMinutes(fake()->numberBetween(1, 30));
                $timestamps['cancellation_reason'] = fake()->randomElement(['Customer requested', 'Out of stock', 'Restaurant closed', 'Too busy']);
                $timestamps['cancelled_by'] = null; // null = customer cancelled
                break;
        }

        return $timestamps;
    }

    private function mapOrderStatusToDeliveryStatus(string $orderStatus): string
    {
        return match ($orderStatus) {
            OrderStatus::Ready->value => 'assigned',
            OrderStatus::PickedUp->value, OrderStatus::OnTheWay->value => 'picked_up',
            OrderStatus::Delivered->value, OrderStatus::Completed->value => 'delivered',
            default => 'assigned',
        };
    }
}
