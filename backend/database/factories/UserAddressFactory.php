<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Home',
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'city' => fake()->city(),
            'state' => fake()->optional()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'US',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'is_default' => true,
            'instructions' => null,
        ];
    }
}
