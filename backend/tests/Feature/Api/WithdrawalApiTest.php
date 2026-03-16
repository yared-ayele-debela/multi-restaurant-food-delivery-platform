<?php

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

function createOwnerWithRestaurantWallet(float $balance): array
{
    $owner = User::factory()->create();
    $owner->assignRole('restaurant_owner');

    $restaurant = Restaurant::factory()->create([
        'owner_id' => $owner->id,
        'is_active' => true,
        'status' => Restaurant::STATUS_APPROVED,
    ]);

    $wallet = Wallet::query()->firstOrCreate(
        [
            'holder_type' => Restaurant::class,
            'holder_id' => $restaurant->id,
        ],
        [
            'balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
            'total_commission_paid' => 0,
            'currency' => 'USD',
            'is_active' => true,
        ]
    );

    $wallet->update([
        'balance' => $balance,
        'total_earned' => $balance,
    ]);

    return [$owner, $restaurant, $wallet->fresh()];
}

test('restaurant cannot request withdrawal above balance', function () {
    [$owner, $restaurant] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 100.01,
    ])->assertUnprocessable();
});

test('admin completes withdrawal debits wallet and writes audit transaction', function () {
    [$owner, $restaurant, $wallet] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $create = $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 40.00,
        'bank_name' => 'Test Bank',
    ]);
    $create->assertCreated();
    $wid = $create->json('data.id');

    expect((float) $wallet->fresh()->balance)->toBe(100.00);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $done = $this->postJson("/api/v1/admin/withdrawals/{$wid}/complete", []);
    $done->assertOk();
    $done->assertJsonPath('data.status', 'completed');

    expect((float) $wallet->fresh()->balance)->toBe(60.00);
    expect((float) $wallet->fresh()->total_withdrawn)->toBe(40.00);

    expect(WalletTransaction::query()
        ->where('wallet_id', $wallet->id)
        ->where('transaction_type', 'withdrawal')
        ->where('reference_type', WithdrawalRequest::class)
        ->where('reference_id', $wid)
        ->count())->toBe(1);
});

test('completing same withdrawal twice is rejected', function () {
    [$owner, $restaurant] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $wid = $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 10.00,
    ])->assertCreated()->json('data.id');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $this->postJson("/api/v1/admin/withdrawals/{$wid}/complete", [])->assertOk();
    $this->postJson("/api/v1/admin/withdrawals/{$wid}/complete", [])->assertUnprocessable();
});

test('admin completes withdrawal fails if balance dropped below amount', function () {
    [$owner, $restaurant, $wallet] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $wid = $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 100.00,
    ])->assertCreated()->json('data.id');

    $wallet->update(['balance' => 20.00]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $this->postJson("/api/v1/admin/withdrawals/{$wid}/complete", [])->assertUnprocessable();
});

test('admin can reject without changing balance', function () {
    [$owner, $restaurant, $wallet] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $wid = $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 25.00,
    ])->assertCreated()->json('data.id');

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $this->postJson("/api/v1/admin/withdrawals/{$wid}/reject", [
        'rejection_reason' => 'Invalid bank details',
    ])->assertOk()->assertJsonPath('data.status', 'rejected');

    expect((float) $wallet->fresh()->balance)->toBe(100.00);
    expect(WalletTransaction::query()->where('reference_type', WithdrawalRequest::class)->where('reference_id', $wid)->count())->toBe(0);
});

test('non admin cannot complete withdrawal', function () {
    [$owner, $restaurant] = createOwnerWithRestaurantWallet(100.00);
    Sanctum::actingAs($owner);

    $wid = $this->postJson('/api/v1/restaurant/wallet/withdrawals', [
        'restaurant_id' => $restaurant->id,
        'amount' => 10.00,
    ])->assertCreated()->json('data.id');

    $this->postJson("/api/v1/admin/withdrawals/{$wid}/complete", [])->assertForbidden();
});
