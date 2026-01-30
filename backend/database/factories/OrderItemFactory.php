<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_size_id' => null,
            'product_name' => fake()->words(3, true),
            'product_size_name' => null,
            'item_name' => null, // Will be computed
            'unit_price' => fake()->randomFloat(2, 5, 35),
            'quantity' => fake()->numberBetween(1, 4),
            'addons' => null,
            'addons_total' => 0,
            'subtotal' => 0, // Will be computed
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (OrderItem $item) {
            // Ensure product is from the same restaurant as the order
            if ($item->order_id && !$item->product_id) {
                $order = Order::find($item->order_id);
                if ($order) {
                    $product = Product::where('restaurant_id', $order->restaurant_id)->inRandomOrder()->first();
                    if ($product) {
                        $item->product_id = $product->id;
                        $item->product_name = $product->name;
                        $item->unit_price = $product->base_price;
                        
                        // Get a random size if available
                        $size = ProductSize::where('product_id', $product->id)->inRandomOrder()->first();
                        if ($size) {
                            $item->product_size_id = $size->id;
                            $item->product_size_name = $size->name;
                            $item->unit_price = $size->price;
                        }
                    }
                }
            }

            // Set item name
            if ($item->product_size_name) {
                $item->item_name = $item->product_name . ' (' . $item->product_size_name . ')';
            } else {
                $item->item_name = $item->product_name;
            }

            // Add random addons
            if (fake()->boolean(40) && $item->product_id) {
                $addons = [];
                $addonsTotal = 0;
                $addonNames = ['Extra Cheese', 'Bacon', 'Mushrooms', 'Avocado', 'Spicy Sauce'];
                $selectedAddons = fake()->randomElements($addonNames, fake()->numberBetween(0, 3));
                
                foreach ($selectedAddons as $addonName) {
                    $price = fake()->randomFloat(2, 0.5, 3);
                    $addons[] = [
                        'name' => $addonName,
                        'price' => $price,
                    ];
                    $addonsTotal += $price;
                }
                
                $item->addons = $addons;
                $item->addons_total = $addonsTotal;
            }

            // Calculate subtotal
            $item->subtotal = ($item->unit_price * $item->quantity) + $item->addons_total;
        });
    }
}
