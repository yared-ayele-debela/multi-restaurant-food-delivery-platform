<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $permissions = [
            // Admin / Super Admin Permissions
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',

            // Restaurant Management (Admin)
            'restaurants.view', 'restaurants.create', 'restaurants.edit', 'restaurants.delete',
            'restaurants.approve', 'restaurants.reject', 'restaurants.suspend',

            // Restaurant Owner Permissions
            'restaurant.manage', 'restaurant.owner',

            // Menu Management
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'product_sizes.view', 'product_sizes.create', 'product_sizes.edit', 'product_sizes.delete',
            'product_addons.view', 'product_addons.create', 'product_addons.edit', 'product_addons.delete',
            'product_stock.view', 'product_stock.create', 'product_stock.edit', 'product_stock.delete',

            // Order Management
            'orders.view', 'orders.create', 'orders.edit', 'orders.cancel', 'orders.manage_restaurant',

            // Driver Permissions
            'orders.accept', 'orders.pickup', 'orders.deliver', 'delivery.manage',

            // Wallet / Financial
            'wallet.view', 'wallet.manage', 'wallet.manage_restaurant',

            // Customer Permissions
            'orders.place', 'addresses.manage', 'profile.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
