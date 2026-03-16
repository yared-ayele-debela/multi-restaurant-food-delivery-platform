<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

function makeCouponableCheckoutContext(): array
{
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 0,
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 100,
    ]);

    $user = User::factory()->create();
    $user->assignRole('customer');
    Sanctum::actingAs($user);

    return [$restaurant, $product, $user];
}

test('checkout rejects invalid coupon code', function () {
    [$restaurant, $product] = makeCouponableCheckoutContext();

    $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'coupon_code' => 'NOPE',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertUnprocessable();
});

test('checkout rejects expired coupon', function () {
    [$restaurant, $product] = makeCouponableCheckoutContext();

    Coupon::query()->create([
        'code' => 'OLD',
        'name' => 'Old',
        'type' => 'percentage',
        'value' => 10,
        'min_order_amount' => 0,
        'restaurant_id' => null,
        'is_first_order_only' => false,
        'max_uses' => null,
        'max_uses_per_user' => null,
        'current_uses' => 0,
        'starts_at' => now()->subMonth(),
        'expires_at' => now()->subDay(),
        'is_active' => true,
    ]);

    $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'coupon_code' => 'OLD',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertUnprocessable();
});

test('checkout rejects restaurant scoped coupon for another restaurant', function () {
    [$restaurant, $product] = makeCouponableCheckoutContext();

    $otherRestaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 0,
    ]);

    Coupon::query()->create([
        'code' => 'OTHER',
        'name' => 'Other only',
        'type' => 'fixed',
        'value' => 5,
        'min_order_amount' => 0,
        'restaurant_id' => $otherRestaurant->id,
        'is_first_order_only' => false,
        'max_uses' => null,
        'max_uses_per_user' => null,
        'current_uses' => 0,
        'starts_at' => null,
        'expires_at' => null,
        'is_active' => true,
    ]);

    $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'coupon_code' => 'OTHER',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertUnprocessable();
});

test('checkout applies valid percentage coupon and records usage', function () {
    [$restaurant, $product, $user] = makeCouponableCheckoutContext();

    $coupon = Coupon::query()->create([
        'code' => 'SAVE10',
        'name' => '10%',
        'type' => 'percentage',
        'value' => 10,
        'min_order_amount' => 0,
        'max_discount_amount' => 50,
        'restaurant_id' => null,
        'is_first_order_only' => false,
        'max_uses' => null,
        'max_uses_per_user' => 5,
        'current_uses' => 0,
        'starts_at' => null,
        'expires_at' => null,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'coupon_code' => 'save10',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);
    $response->assertCreated();
    $response->assertJsonPath('data.discount_amount', '10.00');
    $response->assertJsonPath('data.coupon_id', $coupon->id);

    expect(CouponUsage::query()->where('coupon_id', $coupon->id)->where('user_id', $user->id)->count())->toBe(1);
    expect((int) $coupon->fresh()->current_uses)->toBe(1);
});
