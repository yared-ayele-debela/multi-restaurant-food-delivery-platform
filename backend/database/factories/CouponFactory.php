<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);
        $value = $type === 'percentage' ? fake()->numberBetween(10, 30) : fake()->randomFloat(2, 5, 20);

        return [
            'code' => strtoupper(fake()->unique()->bothify('????##')),
            'name' => fake()->words(2, true).' Discount',
            'description' => fake()->optional()->sentence(),
            'type' => $type,
            'value' => $value,
            'min_order_amount' => fake()->optional()->randomFloat(2, 10, 30),
            'max_discount_amount' => $type === 'percentage' ? fake()->optional()->randomFloat(2, 20, 50) : null,
            'restaurant_id' => Restaurant::factory(),
            'is_first_order_only' => fake()->boolean(20),
            'max_uses' => fake()->optional()->numberBetween(50, 200),
            'max_uses_per_user' => fake()->numberBetween(1, 3),
            'current_uses' => 0,
            'starts_at' => now(),
            'expires_at' => fake()->optional()->dateTimeBetween('+1 week', '+3 months'),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
