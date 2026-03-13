<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create sample customers
        $customers = [
            ['name' => 'Abebe Kebede', 'email' => 'abebe@example.com'],
            ['name' => 'Meron Tadesse', 'email' => 'meron@example.com'],
            ['name' => 'Dawit Hailu', 'email' => 'dawit@example.com'],
            ['name' => 'Selam Alemu', 'email' => 'selam@example.com'],
            ['name' => 'Yonas Girma', 'email' => 'yonas@example.com'],
        ];

        foreach ($customers as $customerData) {
            User::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'phone' => fake()->phoneNumber(),
                ]
            )->assignRole('customer');
        }

        // Create additional random customers
        User::factory()->count(15)->create()->each(function ($user) {
            $user->assignRole('customer');
        });
    }
}
