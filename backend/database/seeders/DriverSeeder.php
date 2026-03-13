<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Driver 1
        $driver1 = User::firstOrCreate(
            ['email' => 'driver1@example.com'],
            [
                'name' => 'John Driver',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '555-0201',
            ]
        );
        $driver1->syncRoles(['driver']);

        Driver::firstOrCreate(
            ['user_id' => $driver1->id],
            [
                'vehicle_type' => 'motorcycle',
                'vehicle_plate' => 'ABC123',
                'license_number' => 'DL001',
                'is_active' => true,
                'is_available' => true,
                'current_latitude' => 40.7128,
                'current_longitude' => -74.0060,
            ]
        );

        // Driver 2
        $driver2 = User::firstOrCreate(
            ['email' => 'driver2@example.com'],
            [
                'name' => 'Jane Driver',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '555-0202',
            ]
        );
        $driver2->syncRoles(['driver']);

        Driver::firstOrCreate(
            ['user_id' => $driver2->id],
            [
                'vehicle_type' => 'car',
                'vehicle_plate' => 'XYZ789',
                'license_number' => 'DL002',
                'is_active' => true,
                'is_available' => true,
                'current_latitude' => 34.0522,
                'current_longitude' => -118.2437,
            ]
        );

        // Driver 3
        $driver3 = User::firstOrCreate(
            ['email' => 'driver3@example.com'],
            [
                'name' => 'Mike Driver',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '555-0203',
            ]
        );
        $driver3->syncRoles(['driver']);

        Driver::firstOrCreate(
            ['user_id' => $driver3->id],
            [
                'vehicle_type' => 'bicycle',
                'vehicle_plate' => null,
                'license_number' => 'DL003',
                'is_active' => true,
                'is_available' => false,
                'current_latitude' => 41.8781,
                'current_longitude' => -87.6298,
            ]
        );
    }
}
