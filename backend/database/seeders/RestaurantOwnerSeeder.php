<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantOwnerSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Restaurant Owner 1
        $owner1 = User::firstOrCreate(
            ['email' => 'owner1@example.com'],
            [
                'name' => 'Restaurant Owner 1',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner1->syncRoles(['restaurant-owner']);

        // Create a restaurant for owner 1
        $restaurant1 = Restaurant::firstOrCreate(
            ['slug' => 'tasty-bites'],
            [
                'owner_id' => $owner1->id,
                'name' => 'Tasty Bites',
                'description' => 'Delicious food at affordable prices',
                'phone' => '555-0101',
                'address_line' => '123 Main St',
                'city' => 'New York',
                'postal_code' => '10001',
                'country' => 'USA',
                'delivery_fee' => 2.99,
                'minimum_order_amount' => 15.00,
                'commission_rate' => 10.00,
                'is_active' => true,
                'status' => 'approved',
            ]
        );

        // Restaurant Owner 2
        $owner2 = User::firstOrCreate(
            ['email' => 'owner2@example.com'],
            [
                'name' => 'Restaurant Owner 2',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner2->syncRoles(['restaurant-owner']);

        // Create a restaurant for owner 2
        $restaurant2 = Restaurant::firstOrCreate(
            ['slug' => 'pizza-palace'],
            [
                'owner_id' => $owner2->id,
                'name' => 'Pizza Palace',
                'description' => 'Best pizza in town',
                'phone' => '555-0102',
                'address_line' => '456 Oak Ave',
                'city' => 'Los Angeles',
                'postal_code' => '90001',
                'country' => 'USA',
                'delivery_fee' => 3.99,
                'minimum_order_amount' => 20.00,
                'commission_rate' => 12.00,
                'is_active' => true,
                'status' => 'approved',
            ]
        );

        // Restaurant Owner 3
        $owner3 = User::firstOrCreate(
            ['email' => 'owner3@example.com'],
            [
                'name' => 'Restaurant Owner 3',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner3->syncRoles(['restaurant-owner']);

        // Create a restaurant for owner 3
        $restaurant3 = Restaurant::firstOrCreate(
            ['slug' => 'burger-kingdom'],
            [
                'owner_id' => $owner3->id,
                'name' => 'Burger Kingdom',
                'description' => 'Gourmet burgers and fries',
                'phone' => '555-0103',
                'address_line' => '789 Burger Blvd',
                'city' => 'Chicago',
                'postal_code' => '60601',
                'country' => 'USA',
                'delivery_fee' => 1.99,
                'minimum_order_amount' => 10.00,
                'commission_rate' => 8.00,
                'is_active' => true,
                'status' => 'approved',
            ]
        );
    }
}
