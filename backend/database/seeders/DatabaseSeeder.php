<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Step 1: Create all permissions
        $this->call(PermissionSeeder::class);

        // Step 2: Create all roles and assign permissions
        $this->call(RoleSeeder::class);

        // Step 3: Create food delivery specific roles and permissions
        $this->call(FoodDeliveryRolesSeeder::class);

        // Step 4: Create users and assign roles
        $this->call(AdminSeeder::class);
        $this->call(RestaurantOwnerSeeder::class);
        $this->call(DriverSeeder::class);
        $this->call(CustomerSeeder::class);

        // Step 5: Create user addresses
        $this->call(UserAddressSeeder::class);

        // Step 6: Create restaurant data (categories, coupons, branches, hours)
        $this->call(CategorySeeder::class);
        $this->call(CouponSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(RestaurantHourSeeder::class);

        // Step 7: Create products with sizes, addons, and stock
        $this->call(ProductSeeder::class);

        // Step 8: Create orders with items and deliveries
        $this->call(OrderSeeder::class);
    }
}
