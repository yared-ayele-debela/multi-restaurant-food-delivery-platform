<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $restaurantOwner = Role::firstOrCreate(['name' => 'restaurant-owner', 'guard_name' => 'web']);
        $driver = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Super Admin - all permissions
        $superAdmin->syncPermissions(Permission::all());

        // Admin - most permissions except super-admin specific
        $adminPermissions = Permission::whereNotIn('name', [
            'roles.delete',
            'permissions.delete',
        ])->get();
        $admin->syncPermissions($adminPermissions);

        // Restaurant Owner - restaurant and menu management
        $restaurantOwnerPermissions = [
            'restaurant.manage', 'restaurant.owner',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'product_sizes.view', 'product_sizes.create', 'product_sizes.edit', 'product_sizes.delete',
            'product_addons.view', 'product_addons.create', 'product_addons.edit', 'product_addons.delete',
            'product_stock.view', 'product_stock.create', 'product_stock.edit', 'product_stock.delete',
            'orders.view', 'orders.edit', 'orders.cancel', 'orders.manage_restaurant',
            'wallet.view', 'wallet.manage_restaurant',
            'profile.manage',
        ];
        $restaurantOwner->syncPermissions($restaurantOwnerPermissions);

        // Driver - delivery related
        $driverPermissions = [
            'orders.view', 'orders.accept', 'orders.pickup', 'orders.deliver', 'delivery.manage',
            'wallet.view', 'profile.manage',
        ];
        $driver->syncPermissions($driverPermissions);

        // Customer - basic permissions
        $customerPermissions = [
            'orders.place', 'orders.view', 'orders.cancel',
            'addresses.manage', 'profile.manage',
        ];
        $customer->syncPermissions($customerPermissions);
    }
}
