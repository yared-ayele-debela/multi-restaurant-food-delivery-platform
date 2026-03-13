<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Get all customers
        $customers = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->get();

        // If no customers, create some regular users
        if ($customers->isEmpty()) {
            $customers = User::factory()->count(10)->create();
            foreach ($customers as $customer) {
                $customer->assignRole('customer');
            }
        }

        foreach ($customers as $customer) {
            // Create 1-2 addresses per customer
            $addressCount = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $addressCount; $i++) {
                UserAddress::firstOrCreate(
                    [
                        'user_id' => $customer->id,
                        'label' => $i === 0 ? 'Home' : 'Work',
                    ],
                    [
                        'address_line_1' => fake()->streetAddress(),
                        'address_line_2' => fake()->optional()->secondaryAddress(),
                        'city' => fake()->randomElement(['Addis Ababa', 'Dire Dawa', 'Hawassa', 'Bahir Dar']),
                        'state' => fake()->randomElement(['Addis Ababa', 'Oromia', 'Amhara', 'SNNPR']),
                        'postal_code' => fake()->postcode(),
                        'country' => 'ET',
                        'latitude' => fake()->latitude(8, 10),
                        'longitude' => fake()->longitude(38, 40),
                        'is_default' => $i === 0,
                        'instructions' => fake()->optional()->sentence(),
                    ]
                );
            }
        }
    }
}
