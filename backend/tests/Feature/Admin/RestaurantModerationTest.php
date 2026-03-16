<?php

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\FoodDeliveryRolesSeeder;

beforeEach(function () {
    $this->seed(FoodDeliveryRolesSeeder::class);
});

function adminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

test('guest cannot access admin restaurants', function () {
    $this->get(route('admin.restaurants.index'))->assertRedirect();
});

test('non-admin cannot access admin restaurants', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $this->actingAs($user)->get(route('admin.restaurants.index'))->assertForbidden();
});

test('admin can list and moderate restaurants', function () {
    $admin = adminUser();
    $pending = Restaurant::factory()->create([
        'status' => Restaurant::STATUS_PENDING,
        'slug' => 'pending-cafe',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.restaurants.index', ['status' => 'pending']))
        ->assertOk()
        ->assertSee('pending-cafe');

    $this->actingAs($admin)
        ->post(route('admin.restaurants.approve', $pending))
        ->assertRedirect();

    $pending->refresh();
    expect($pending->status)->toBe(Restaurant::STATUS_APPROVED)
        ->and($pending->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.restaurants.suspend', $pending))
        ->assertRedirect();

    $pending->refresh();
    expect($pending->status)->toBe(Restaurant::STATUS_SUSPENDED)
        ->and($pending->is_active)->toBeFalse();

    $this->actingAs($admin)
        ->post(route('admin.restaurants.reject', $pending))
        ->assertRedirect();

    $pending->refresh();
    expect($pending->status)->toBe(Restaurant::STATUS_REJECTED);

    $this->actingAs($admin)
        ->post(route('admin.restaurants.toggle-featured', $pending))
        ->assertRedirect();

    $pending->refresh();
    expect($pending->is_featured)->toBeTrue();
});
