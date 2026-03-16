<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HourController extends Controller
{
    private const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function index(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageHours', $restaurant);

        $hours = RestaurantHour::where('restaurant_id', $restaurant->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('restaurant.hours.index', [
            'hours' => $hours,
            'days' => self::DAYS,
            'restaurant' => $restaurant,
        ]);
    }

    public function edit(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageHours', $restaurant);

        $hours = RestaurantHour::where('restaurant_id', $restaurant->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('restaurant.hours.edit', [
            'hours' => $hours,
            'days' => self::DAYS,
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageHours', $restaurant);

        foreach (self::DAYS as $day => $dayName) {
            $dayData = $request->input("days.{$day}", []);

            $isClosed = !empty($dayData['is_closed']);

            $hourData = [
                'restaurant_id' => $restaurant->id,
                'day_of_week' => $day,
                'is_closed' => $isClosed,
                'open_time' => $isClosed ? null : ($dayData['open_time'] ?? '09:00'),
                'close_time' => $isClosed ? null : ($dayData['close_time'] ?? '22:00'),
            ];

            RestaurantHour::updateOrCreate(
                ['restaurant_id' => $restaurant->id, 'day_of_week' => $day],
                $hourData
            );
        }

        return redirect()->route('restaurant.hours.index')
            ->with('success', 'Operating hours updated successfully.');
    }
}
