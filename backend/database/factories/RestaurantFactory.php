<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numerify('###')),
            'description' => fake()->optional()->sentence(),
            'phone' => fake()->phoneNumber(),
            'address_line' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->optional()->postcode(),
            'country' => 'ET',
            'latitude' => fake()->optional()->latitude(),
            'longitude' => fake()->optional()->longitude(),
            'delivery_fee' => fake()->randomFloat(2, 0, 5),
            'minimum_order_amount' => fake()->randomFloat(2, 0, 15),
            'commission_rate' => 15.00,
            'is_active' => true,
            'status' => Restaurant::STATUS_APPROVED,
            'is_featured' => false,
        ];
    }
}
