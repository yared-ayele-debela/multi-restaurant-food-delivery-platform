<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

test('customer list orders returns only own orders', function () {
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 0,
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 5,
    ]);

    $a = User::factory()->create();
    $a->assignRole('customer');
    $b = User::factory()->create();
    $b->assignRole('customer');

    Sanctum::actingAs($a);
    $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'A',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertCreated();

    Sanctum::actingAs($b);
    $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'B',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertCreated();

    Sanctum::actingAs($a);
    $list = $this->getJson('/api/v1/orders');
    $list->assertOk();
    expect($list->json('data'))->toHaveCount(1);
    expect($list->json('data.0.delivery_address.formatted'))->toBe('A');
});

test('customer order detail includes items and filtered status history', function () {
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 0,
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 5,
    ]);

    $user = User::factory()->create();
    $user->assignRole('customer');
    Sanctum::actingAs($user);

    $create = $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);
    $create->assertCreated();
    $orderId = $create->json('data.id');

    $detail = $this->getJson('/api/v1/orders/'.$orderId);
    $detail->assertOk();
    $detail->assertJsonPath('data.items.0.product_name', $product->name);
    $detail->assertJsonStructure([
        'data' => [
            'status_history' => [
                [
                    'previous_status',
                    'new_status',
                    'changed_at',
                    'actor',
                ],
            ],
        ],
    ]);
    expect($detail->json('data.status_history'))->toHaveCount(1);
    expect($detail->json('data.status_history.0.new_status'))->toBe('pending');
    expect($detail->json('data.status_history.0.actor.name'))->toBe($user->name);

    $row = $detail->json('data.status_history.0');
    expect($row)->not->toHaveKey('notes');
    expect($row)->not->toHaveKey('changed_by');
});
