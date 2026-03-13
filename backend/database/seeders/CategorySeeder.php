<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Get all restaurants
        $restaurants = Restaurant::all();

        $categoryNames = [
            'Appetizers',
            'Main Courses',
            'Desserts',
            'Beverages',
            'Sides',
            'Specials',
            'Breakfast',
            'Lunch',
            'Dinner',
            'Kids Menu',
        ];

        foreach ($restaurants as $restaurant) {
            foreach ($categoryNames as $index => $name) {
                Category::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'slug' => \Illuminate\Support\Str::slug($name.'-'.$restaurant->id),
                    ],
                    [
                        'name' => $name,
                        'description' => fake()->optional()->sentence(),
                        'image' => null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
