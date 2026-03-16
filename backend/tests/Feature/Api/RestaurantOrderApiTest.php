<?php

use App\Enums\OrderStatus;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

function createRestaurantOrderForOwner(User $owner): Order
{
    $restaurant = Restaurant::factory()->create([
        'owner_id' => $owner->id,
        'latitude' => 9.03,
        'longitude' => 38.75,
        'is_active' => true,
        'status' => \App\Models\Restaurant::STATUS_APPROVED,
    ]);

    $customer = User::factory()->create();

    return Order::query()->create([
        'order_number' => 'ORD-R-'.uniqid(),
        'user_id' => $customer->id,
        'restaurant_id' => $restaurant->id,
        'status' => OrderStatus::Pending,
        'subtotal' => 20,
        'discount_amount' => 0,
        'delivery_fee' => 0,
        'tax_amount' => 0,
        'tax_rate' => 0,
        'total' => 20,
        'commission_rate' => 10,
        'commission_amount' => 2,
        'restaurant_earnings' => 18,
        'driver_earnings' => 0,
        'payment_method' => 'cash',
        'payment_status' => 'pending',
        'delivery_address' => [
            'formatted' => 'Drop',
            'latitude' => '9.04',
            'longitude' => '38.76',
        ],
        'placed_at' => now(),
    ]);
}

test('restaurant owner can accept preparing and ready and creates delivery', function () {
    $owner = User::factory()->create();
    $owner->assignRole('restaurant_owner');
    Sanctum::actingAs($owner);

    $order = createRestaurantOrderForOwner($owner);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/accept")->assertOk();
    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Accepted);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/preparing")->assertOk();
    expect($order->fresh()->status)->toBe(OrderStatus::Preparing);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/ready")->assertOk();
    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Ready);
    expect($order->delivery)->not->toBeNull();
    expect($order->delivery->status)->toBe('pending');
});

test('restaurant owner can assign approved driver to delivery', function () {
    $owner = User::factory()->create();
    $owner->assignRole('restaurant_owner');
    Sanctum::actingAs($owner);

    $order = createRestaurantOrderForOwner($owner);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/accept")->assertOk();
    $this->postJson("/api/v1/restaurant/orders/{$order->id}/preparing")->assertOk();
    $this->postJson("/api/v1/restaurant/orders/{$order->id}/ready")->assertOk();

    $driverUser = User::factory()->create();
    $driver = Driver::factory()->approved()->create(['user_id' => $driverUser->id]);

    $response = $this->postJson("/api/v1/restaurant/orders/{$order->id}/assign-driver", [
        'driver_id' => $driver->id,
    ]);
    $response->assertOk();
    expect($response->json('data.delivery.status'))->toBe('assigned');
    expect($response->json('data.delivery.driver_id'))->toBe($driver->id);
});

test('non owner cannot transition restaurant order', function () {
    $owner = User::factory()->create();
    $owner->assignRole('restaurant_owner');

    $other = User::factory()->create();
    $other->assignRole('restaurant_owner');
    Sanctum::actingAs($other);

    $order = createRestaurantOrderForOwner($owner);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/accept")->assertForbidden();
});

test('cannot assign unapproved driver', function () {
    $owner = User::factory()->create();
    $owner->assignRole('restaurant_owner');
    Sanctum::actingAs($owner);

    $order = createRestaurantOrderForOwner($owner);
    $this->postJson("/api/v1/restaurant/orders/{$order->id}/accept")->assertOk();
    $this->postJson("/api/v1/restaurant/orders/{$order->id}/preparing")->assertOk();
    $this->postJson("/api/v1/restaurant/orders/{$order->id}/ready")->assertOk();

    $driver = Driver::factory()->create(['is_approved' => false]);

    $this->postJson("/api/v1/restaurant/orders/{$order->id}/assign-driver", [
        'driver_id' => $driver->id,
    ])->assertUnprocessable();
});
