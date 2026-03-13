<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantHourSeeder extends Seeder
{
    use WithoutModelEvents;

    private const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            foreach (self::DAYS as $day => $dayName) {
                // Randomly close some days (10% chance)
                $isClosed = fake()->boolean(10);

                RestaurantHour::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'open_time' => '09:00',
                        'close_time' => '22:00',
                        'is_closed' => $isClosed,
                    ]
                );
            }
        }
    }
}
