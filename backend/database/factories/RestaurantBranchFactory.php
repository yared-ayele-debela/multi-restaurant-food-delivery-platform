<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantBranch>
 */
class RestaurantBranchFactory extends Factory
{
    protected $model = RestaurantBranch::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->words(2, true).' Branch',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->optional()->state(),
            'postal_code' => fake()->postcode(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'phone' => fake()->optional()->phoneNumber(),
            'delivery_radius' => fake()->randomFloat(2, 3, 25),
            'preparation_time' => fake()->numberBetween(15, 45),
            'is_active' => true,
        ];
    }
}
