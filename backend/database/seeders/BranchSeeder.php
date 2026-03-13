<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            // Each restaurant gets 1-3 branches
            $branchCount = fake()->numberBetween(1, 3);

            for ($i = 1; $i <= $branchCount; $i++) {
                RestaurantBranch::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $i === 1 ? 'Main Branch' : "Branch {$i}",
                    ],
                    [
                        'address' => fake()->streetAddress(),
                        'city' => fake()->randomElement(['Addis Ababa', 'Dire Dawa', 'Hawassa', 'Bahir Dar', 'Mekelle']),
                        'state' => fake()->randomElement(['Addis Ababa', 'Oromia', 'Amhara', 'Tigray', 'SNNPR']),
                        'postal_code' => fake()->postcode(),
                        'latitude' => fake()->latitude(8, 14),
                        'longitude' => fake()->longitude(36, 43),
                        'phone' => fake()->phoneNumber(),
                        'delivery_radius' => fake()->randomFloat(2, 3, 15),
                        'preparation_time' => fake()->numberBetween(15, 45),
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
