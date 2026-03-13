<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $restaurants = Restaurant::all();
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        $couponTemplates = [
            ['suffix' => 'WELCOME20', 'name' => 'Welcome Discount', 'type' => 'percentage', 'value' => 20],
            ['suffix' => 'SAVE10', 'name' => 'Save 10%', 'type' => 'percentage', 'value' => 10],
            ['suffix' => 'FLAT5', 'name' => '$5 Off', 'type' => 'fixed', 'value' => 5],
            ['suffix' => 'FREEDEL', 'name' => 'Free Delivery', 'type' => 'fixed', 'value' => 2.99],
            ['suffix' => 'FIRSTORDER', 'name' => 'First Order Special', 'type' => 'percentage', 'value' => 25, 'first_order_only' => true],
        ];

        foreach ($restaurants as $restaurant) {
            foreach ($couponTemplates as $template) {
                // Make code unique per restaurant
                $code = $template['suffix'] . $restaurant->id;
                Coupon::firstOrCreate(
                    [
                        'code' => $code,
                        'restaurant_id' => $restaurant->id,
                    ],
                    [
                        'name' => $template['name'],
                        'description' => fake()->sentence(),
                        'type' => $template['type'],
                        'value' => $template['value'],
                        'min_order_amount' => $template['type'] === 'fixed' ? 20 : 0,
                        'max_discount_amount' => $template['type'] === 'percentage' ? 30 : null,
                        'is_first_order_only' => $template['first_order_only'] ?? false,
                        'max_uses' => 100,
                        'max_uses_per_user' => 1,
                        'current_uses' => 0,
                        'starts_at' => now(),
                        'expires_at' => now()->addMonths(3),
                        'is_active' => true,
                        'created_by' => $admin?->id,
                    ]
                );
            }
        }
    }
}
