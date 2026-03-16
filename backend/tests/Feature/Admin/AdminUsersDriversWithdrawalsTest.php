<?php

use App\Models\Driver;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

function actingAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

function ownerRestaurantWallet(float $balance): array
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

test('admin can suspend and reactivate a user', function () {
    $admin = actingAdmin();
    $target = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $this->actingAs($admin)
        ->post(route('admin.users.suspend', $target))
        ->assertRedirect();

    expect($target->fresh()->status)->toBe(User::STATUS_SUSPENDED);

    $this->actingAs($admin)
        ->post(route('admin.users.activate', $target))
        ->assertRedirect();

    expect($target->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

test('admin cannot suspend super-admin', function () {
    Role::query()->firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

    $admin = actingAdmin();
    $super = User::factory()->create(['status' => User::STATUS_ACTIVE]);
    $super->assignRole('super-admin');

    $this->actingAs($admin)
        ->post(route('admin.users.suspend', $super))
        ->assertRedirect();

    expect($super->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

test('admin cannot suspend self', function () {
    $admin = actingAdmin();

    $this->actingAs($admin)
        ->post(route('admin.users.suspend', $admin))
        ->assertRedirect();

    expect($admin->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

test('admin can approve a driver from web', function () {
    $admin = actingAdmin();
    $driverUser = User::factory()->create();
    $driverUser->assignRole('driver');

    $driver = Driver::factory()->create([
        'user_id' => $driverUser->id,
        'is_approved' => false,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.drivers.approve', $driver))
        ->assertRedirect(route('admin.drivers.show', $driver));

    expect($driver->fresh()->is_approved)->toBeTrue()
        ->and($driver->fresh()->approved_at)->not->toBeNull();
});

test('admin completes withdrawal with notes via web', function () {
    [, , $wallet] = ownerRestaurantWallet(100.00);

    $withdrawal = WithdrawalRequest::query()->create([
        'wallet_id' => $wallet->id,
        'amount' => 25.00,
        'bank_name' => 'Test',
        'status' => 'pending',
    ]);

    $admin = actingAdmin();

    $this->actingAs($admin)
        ->post(route('admin.withdrawals.complete', $withdrawal), [
            'admin_notes' => 'Paid via transfer',
        ])
        ->assertRedirect();

    $withdrawal->refresh();
    expect($withdrawal->status)->toBe('completed')
        ->and($withdrawal->processed_by)->toBe($admin->id)
        ->and($withdrawal->admin_notes)->toBe('Paid via transfer');

    expect((float) $wallet->fresh()->balance)->toBe(75.00);
});

test('admin rejects withdrawal via web', function () {
    [, , $wallet] = ownerRestaurantWallet(50.00);

    $withdrawal = WithdrawalRequest::query()->create([
        'wallet_id' => $wallet->id,
        'amount' => 10.00,
        'status' => 'pending',
    ]);

    $admin = actingAdmin();

    $this->actingAs($admin)
        ->post(route('admin.withdrawals.reject', $withdrawal), [
            'rejection_reason' => 'Invalid account',
            'admin_notes' => 'Please update bank details',
        ])
        ->assertRedirect();

    $withdrawal->refresh();
    expect($withdrawal->status)->toBe('rejected')
        ->and($withdrawal->rejection_reason)->toBe('Invalid account')
        ->and($withdrawal->processed_by)->toBe($admin->id);

    expect((float) $wallet->fresh()->balance)->toBe(50.00);
});
