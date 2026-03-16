<?php

use App\Enums\OrderStatus;
use App\Models\LoyaltyPoints;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\OrderStatusTransitionService;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = app(OrderStatusTransitionService::class);
});

function makeTestOrder(?OrderStatus $status = null): Order
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create([
        'latitude' => 9.03,
        'longitude' => 38.75,
    ]);

    return Order::query()->create([
        'order_number' => 'ORD-TEST-'.uniqid(),
        'user_id' => $user->id,
        'restaurant_id' => $restaurant->id,
        'status' => $status ?? OrderStatus::Pending,
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
            'formatted' => '1 Test St',
            'latitude' => '9.04',
            'longitude' => '38.76',
        ],
        'placed_at' => now(),
    ]);
}

test('happy path sets lifecycle timestamps and appends history', function () {
    $order = makeTestOrder();
    $actor = User::factory()->create();

    $this->service->transition($order, OrderStatus::Accepted, $actor);
    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Accepted);
    expect($order->accepted_at)->not->toBeNull();

    $this->service->transition($order, OrderStatus::Preparing, $actor);
    $order->refresh();
    expect($order->preparing_at)->not->toBeNull();

    $this->service->transition($order, OrderStatus::Ready, $actor);
    expect($order->fresh()->delivery)->not->toBeNull();
    $this->service->transition($order, OrderStatus::OnTheWay, $actor);
    $this->service->transition($order, OrderStatus::Delivered, $actor);
    $this->service->transition($order, OrderStatus::Completed, $actor);

    $order->refresh();
    expect($order->ready_at)->not->toBeNull();
    expect($order->delivered_at)->not->toBeNull();
    expect($order->completed_at)->not->toBeNull();
    expect($order->statusHistory()->count())->toBeGreaterThan(1);

    $wallet = $order->restaurant->wallet()->first();
    expect($wallet)->not->toBeNull();
    expect((float) $wallet->balance)->toBe(18.0);
    expect((float) $wallet->total_earned)->toBe(18.0);
    expect((float) $wallet->total_commission_paid)->toBe(2.0);
    expect(WalletTransaction::query()
        ->where('reference_type', Order::class)
        ->where('reference_id', $order->id)
        ->where('transaction_type', 'order_earning')
        ->count())->toBe(1);

    $customer = User::query()->findOrFail($order->user_id);
    $lp = LoyaltyPoints::query()->where('user_id', $customer->id)->first();
    expect($lp)->not->toBeNull();
    expect($lp->available_points)->toBe(200);
    expect(LoyaltyTransaction::query()
        ->where('user_id', $customer->id)
        ->where('source_type', Order::class)
        ->where('source_id', $order->id)
        ->where('type', 'earned')
        ->count())->toBe(1);
});

test('cannot skip from pending to delivered', function () {
    $order = makeTestOrder();

    $this->service->transition($order, OrderStatus::Delivered, null);
})->throws(ValidationException::class);

test('cancellation requires reason and records cancelled fields', function () {
    $order = makeTestOrder();
    $actor = User::factory()->create();

    expect(fn () => $this->service->transition($order, OrderStatus::Cancelled, $actor))
        ->toThrow(ValidationException::class);

    $this->service->transition($order, OrderStatus::Cancelled, $actor, notes: null, context: [
        'cancellation_reason' => 'Customer changed mind',
    ]);

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Cancelled);
    expect($order->cancelled_at)->not->toBeNull();
    expect($order->cancellation_reason)->toBe('Customer changed mind');
    expect($order->cancelled_by)->toBe($actor->id);
});

test('no-op when status unchanged', function () {
    $order = makeTestOrder();
    $before = $order->statusHistory()->count();

    $this->service->transition($order, OrderStatus::Pending, null);

    expect($order->fresh()->statusHistory()->count())->toBe($before);
});
