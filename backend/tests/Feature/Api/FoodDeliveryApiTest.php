<?php

use App\Models\Category;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductSize;
use App\Models\ProductStock;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use App\Models\RestaurantHour;
use App\Models\RestaurantImage;
use App\Models\User;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

test('guest can hit api health endpoint', function () {
    $response = $this->getJson('/api/v1/health');

    $response->assertOk();
    $response->assertJsonPath('status', 'ok');
    $response->assertJsonPath('api', 'v1');
});

test('guest can list active restaurants', function () {
    Restaurant::factory()->create(['is_active' => true, 'name' => 'Open Place']);
    Restaurant::factory()->create(['is_active' => false, 'name' => 'Closed Place']);

    $response = $this->getJson('/api/v1/restaurants');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['name'])->toBe('Open Place');
});

test('guest can view restaurant menu by slug', function () {
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'slug' => 'tasty-bites',
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'name' => 'Burger',
        'slug' => 'burger-1',
        'base_price' => 12.50,
    ]);
    ProductSize::query()->create([
        'product_id' => $product->id,
        'name' => 'Large',
        'price' => 16.00,
        'is_default' => false,
        'sort_order' => 1,
    ]);
    ProductAddon::query()->create([
        'product_id' => $product->id,
        'name' => 'Extra cheese',
        'price' => 1.50,
        'is_active' => true,
        'max_quantity' => 2,
        'group_name' => 'Toppings',
        'sort_order' => 0,
    ]);
    $branch = RestaurantBranch::factory()->create(['restaurant_id' => $restaurant->id]);
    ProductStock::query()->create([
        'product_id' => $product->id,
        'branch_id' => $branch->id,
        'quantity' => 20,
        'low_stock_threshold' => 3,
        'track_stock' => true,
    ]);

    RestaurantImage::query()->create([
        'restaurant_id' => $restaurant->id,
        'image_path' => '/storage/restaurants/hero.jpg',
        'alt_text' => 'Storefront',
        'sort_order' => 0,
        'is_primary' => true,
    ]);
    RestaurantHour::query()->create([
        'restaurant_id' => $restaurant->id,
        'day_of_week' => 1,
        'open_time' => '09:00:00',
        'close_time' => '22:00:00',
        'is_closed' => false,
    ]);

    $response = $this->getJson('/api/v1/restaurants/tasty-bites');

    $response->assertOk();
    $response->assertJsonPath('data.slug', 'tasty-bites');
    $products = $response->json('data.categories.0.products');
    expect($products)->toHaveCount(1);
    expect($products[0]['name'])->toBe('Burger');
    expect($products[0]['sizes'])->toHaveCount(2);
    expect($products[0]['addons'])->toHaveCount(1);
    expect($products[0]['addons'][0]['name'])->toBe('Extra cheese');
    expect($products[0]['stock'])->toHaveCount(2);

    $filtered = $this->getJson('/api/v1/restaurants/tasty-bites?branch_id='.$branch->id);
    $filtered->assertOk();
    expect($filtered->json('data.categories.0.products.0.stock'))->toHaveCount(2);

    expect($response->json('data.images'))->toHaveCount(1);
    expect($response->json('data.images.0.image_path'))->toBe('/storage/restaurants/hero.jpg');
    expect($response->json('data.hours'))->toHaveCount(1);
    expect($response->json('data.hours.0.open_time'))->toBe('09:00');
});

test('guest can view restaurant branches with delivery radius and coordinates', function () {
    $restaurant = Restaurant::factory()->create(['slug' => 'multi-branch', 'is_active' => true]);
    RestaurantBranch::factory()->create([
        'restaurant_id' => $restaurant->id,
        'name' => 'Downtown',
        'latitude' => 9.03,
        'longitude' => 38.75,
        'delivery_radius' => 12.5,
        'is_active' => true,
    ]);
    RestaurantBranch::factory()->create([
        'restaurant_id' => $restaurant->id,
        'name' => 'Closed depot',
        'is_active' => false,
    ]);

    $detail = $this->getJson('/api/v1/restaurants/multi-branch');
    $detail->assertOk();
    expect($detail->json('data.branches'))->toHaveCount(1);
    expect($detail->json('data.branches.0.name'))->toBe('Downtown');
    expect($detail->json('data.branches.0.delivery_radius_km'))->toBe('12.50');

    $only = $this->getJson('/api/v1/restaurants/multi-branch/branches');
    $only->assertOk();
    expect($only->json('data'))->toHaveCount(1);
    expect($only->json('data.0.latitude'))->toBe('9.03000000');
});

test('guest cannot list or view pending or inactive restaurants', function () {
    Restaurant::factory()->create(['name' => 'Pending', 'is_active' => true, 'status' => Restaurant::STATUS_PENDING]);
    Restaurant::factory()->create(['name' => 'Inactive', 'is_active' => false, 'status' => Restaurant::STATUS_APPROVED]);
    $hidden = Restaurant::factory()->create(['slug' => 'hidden', 'is_active' => true, 'status' => Restaurant::STATUS_APPROVED]);
    $hidden->delete();

    $list = $this->getJson('/api/v1/restaurants');
    $list->assertOk();
    expect($list->json('data'))->toHaveCount(0);

    $this->getJson('/api/v1/restaurants/hidden')->assertNotFound();
});

test('customer can register and place an order', function () {
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 2.00,
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 10.00,
    ]);

    $reg = $this->postJson('/api/v1/auth/register', [
        'name' => 'Buyer',
        'email' => 'buyer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $reg->assertCreated();
    $token = $reg->json('token');

    $order = $this->postJson(
        '/api/v1/orders',
        [
            'restaurant_id' => $restaurant->id,
            'delivery_address' => '123 Main St',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ],
        ['Authorization' => 'Bearer '.$token]
    );

    $order->assertCreated();
    $order->assertJsonPath('data.total', '22.00');
    $order->assertJsonPath('data.status', 'pending');
});

test('checkout decrements product stock when tracked and records status history', function () {
    $restaurant = Restaurant::factory()->create([
        'is_active' => true,
        'minimum_order_amount' => 0,
        'delivery_fee' => 0,
    ]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 10,
    ]);
    $stock = ProductStock::query()->where('product_id', $product->id)->first();
    $stock->update(['track_stock' => true, 'quantity' => 5]);

    $user = User::factory()->create();
    $user->assignRole('customer');
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
    ]);
    $response->assertCreated();
    expect($stock->fresh()->quantity)->toBe(3);
    expect(OrderStatusHistory::query()->where('order_id', $response->json('data.id'))->count())->toBe(1);
});

test('user can only view own order', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $restaurant = Restaurant::factory()->create(['is_active' => true, 'minimum_order_amount' => 0, 'delivery_fee' => 0]);
    $category = Category::factory()->create(['restaurant_id' => $restaurant->id]);
    $product = Product::factory()->forRestaurant($restaurant)->create([
        'category_id' => $category->id,
        'base_price' => 5,
    ]);

    Sanctum::actingAs($user);

    $create = $this->postJson('/api/v1/orders', [
        'restaurant_id' => $restaurant->id,
        'delivery_address' => 'Here',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);
    $orderId = $create->json('data.id');

    $other = User::factory()->create();
    Sanctum::actingAs($other);

    $this->getJson('/api/v1/orders/'.$orderId)->assertForbidden();
});
