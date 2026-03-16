<?php

use App\Models\User;
use App\Models\UserAddress;
use Database\Seeders\FoodDeliveryRolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

test('register returns user with roles', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Buyer',
        'email' => 'buyer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('user.roles', ['customer']);
    $response->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'phone', 'avatar', 'status', 'roles']]);
});

test('authenticated user can get and patch profile', function () {
    $user = User::factory()->create(['email' => 'u@example.com']);
    $user->assignRole('customer');
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/auth/user')
        ->assertOk()
        ->assertJsonPath('user.email', 'u@example.com')
        ->assertJsonPath('user.roles', ['customer']);

    $this->patchJson('/api/v1/auth/user', [
        'name' => 'Updated Name',
        'phone' => '+15551234567',
    ])
        ->assertOk()
        ->assertJsonPath('user.name', 'Updated Name')
        ->assertJsonPath('user.phone', '+15551234567');
});

test('user can crud addresses and only one default', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    Sanctum::actingAs($user);

    $a = $this->postJson('/api/v1/user/addresses', [
        'address_line_1' => '100 Main St',
        'city' => 'NYC',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'is_default' => true,
    ]);
    $a->assertCreated();
    $a->assertJsonPath('data.is_default', true);
    $id1 = $a->json('data.id');

    $b = $this->postJson('/api/v1/user/addresses', [
        'label' => 'Work',
        'address_line_1' => '200 Oak Ave',
        'city' => 'NYC',
        'latitude' => 40.7200,
        'longitude' => -74.0100,
        'is_default' => true,
    ]);
    $b->assertCreated();
    $id2 = $b->json('data.id');

    $this->assertDatabaseHas('user_addresses', ['id' => $id1, 'is_default' => false]);
    $this->assertDatabaseHas('user_addresses', ['id' => $id2, 'is_default' => true]);

    $this->patchJson("/api/v1/user/addresses/{$id1}", ['is_default' => true])
        ->assertOk();

    $this->assertDatabaseHas('user_addresses', ['id' => $id1, 'is_default' => true]);
    $this->assertDatabaseHas('user_addresses', ['id' => $id2, 'is_default' => false]);

    $this->getJson('/api/v1/user/addresses')
        ->assertOk()
        ->assertJsonCount(2, 'data');

    $this->deleteJson("/api/v1/user/addresses/{$id2}")
        ->assertNoContent();

    expect(UserAddress::withTrashed()->find($id2)->trashed())->toBeTrue();
});

test('user cannot modify another users address', function () {
    $owner = User::factory()->create();
    $owner->assignRole('customer');
    $other = User::factory()->create();
    $other->assignRole('customer');

    $address = UserAddress::factory()->create(['user_id' => $owner->id]);

    Sanctum::actingAs($other);

    $this->patchJson("/api/v1/user/addresses/{$address->id}", [
        'city' => 'Hacked',
    ])->assertForbidden();

    $this->deleteJson("/api/v1/user/addresses/{$address->id}")
        ->assertForbidden();
});
