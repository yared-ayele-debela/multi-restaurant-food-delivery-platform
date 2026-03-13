<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductSize;
use App\Models\ProductStock;
use App\Models\Restaurant;
use App\Models\RestaurantBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $restaurants = Restaurant::all();

        $productNames = [
            'Classic Burger',
            'Cheeseburger',
            'Double Cheeseburger',
            'Chicken Wings',
            'Caesar Salad',
            'Pasta Carbonara',
            'Margherita Pizza',
            'Pepperoni Pizza',
            'Grilled Salmon',
            'Steak Frites',
            'Fish and Chips',
            'Chicken Sandwich',
            'Veggie Burger',
            'Falafel Wrap',
            'Beef Tacos',
            'Chicken Quesadilla',
            'Chocolate Cake',
            'Ice Cream Sundae',
            'Apple Pie',
            'Milkshake',
            'Fresh Juice',
            'Soft Drink',
            'Coffee',
            'Tea',
            'French Fries',
            'Onion Rings',
            'Garlic Bread',
            'Mozzarella Sticks',
            'Buffalo Wings',
            'Caesar Wrap',
        ];

        foreach ($restaurants as $restaurant) {
            $categories = Category::where('restaurant_id', $restaurant->id)->get();
            $branches = RestaurantBranch::where('restaurant_id', $restaurant->id)->get();

            foreach ($productNames as $index => $productName) {
                $category = $categories->random();

                $product = Product::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'slug' => \Illuminate\Support\Str::slug($productName.'-'.$restaurant->id),
                    ],
                    [
                        'category_id' => $category->id,
                        'name' => $productName,
                        'description' => fake()->optional()->sentence(),
                        'image' => null,
                        'base_price' => fake()->randomFloat(2, 5, 35),
                        'discount_price' => fake()->boolean(20) ? fake()->randomFloat(2, 3, 25) : null,
                        'preparation_time' => fake()->numberBetween(10, 30),
                        'is_active' => true,
                        'is_featured' => fake()->boolean(10),
                        'sort_order' => $index,
                        'dietary_info' => fake()->boolean(30) ? ['vegetarian' => fake()->boolean()] : null,
                        'allergens' => fake()->boolean(20) ? [fake()->randomElement(['nuts', 'dairy', 'gluten'])] : null,
                        'calories' => fake()->optional()->numberBetween(200, 1200),
                    ]
                );

                // Create sizes if not exists
                if (!ProductSize::where('product_id', $product->id)->exists()) {
                    $sizes = [
                        ['name' => 'Small', 'price_multiplier' => 0.8],
                        ['name' => 'Medium', 'price_multiplier' => 1.0],
                        ['name' => 'Large', 'price_multiplier' => 1.3],
                    ];

                    foreach ($sizes as $sizeIndex => $size) {
                        ProductSize::create([
                            'product_id' => $product->id,
                            'name' => $size['name'],
                            'price' => round($product->base_price * $size['price_multiplier'], 2),
                            'is_default' => $size['name'] === 'Medium',
                            'sort_order' => $sizeIndex,
                        ]);
                    }
                }

                // Create addons if not exists
                if (!ProductAddon::where('product_id', $product->id)->exists() && fake()->boolean(60)) {
                    $addonNames = ['Extra Cheese', 'Bacon', 'Avocado', 'Mushrooms', 'Spicy Sauce', 'Double Meat'];
                    foreach (fake()->randomElements($addonNames, fake()->numberBetween(2, 4)) as $addonIndex => $addonName) {
                        ProductAddon::create([
                            'product_id' => $product->id,
                            'name' => $addonName,
                            'price' => fake()->randomFloat(2, 0.5, 3),
                            'is_active' => true,
                            'max_quantity' => fake()->numberBetween(1, 3),
                            'group_name' => fake()->randomElement(['Extras', 'Toppings']),
                            'sort_order' => $addonIndex,
                        ]);
                    }
                }

                // Create stock for each branch
                if (!ProductStock::where('product_id', $product->id)->exists()) {
                    foreach ($branches as $branch) {
                        ProductStock::create([
                            'product_id' => $product->id,
                            'branch_id' => $branch->id,
                            'quantity' => fake()->numberBetween(20, 200),
                            'low_stock_threshold' => fake()->numberBetween(5, 15),
                            'track_stock' => true,
                        ]);
                    }
                }
            }
        }
    }
}
