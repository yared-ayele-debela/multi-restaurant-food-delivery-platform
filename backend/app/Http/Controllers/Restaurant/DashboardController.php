<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('viewDashboard', $restaurant);

        // Get today's stats
        $todayStats = $this->getTodayStats($restaurant->id);

        // Get recent orders
        $recentOrders = Order::where('restaurant_id', $restaurant->id)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Get menu stats
        $menuStats = [
            'categories' => $restaurant->categories()->count(),
            'products' => $restaurant->products()->count(),
            'active_products' => $restaurant->products()->where('is_active', true)->count(),
        ];

        return view('restaurant.dashboard', [
            'restaurant' => $restaurant,
            'todayStats' => $todayStats,
            'recentOrders' => $recentOrders,
            'menuStats' => $menuStats,
        ]);
    }

    private function getTodayStats(int $restaurantId): array
    {
        // Verify user has access to this restaurant
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        if ($restaurant) {
            $this->authorize('viewDashboard', $restaurant);
        }
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $todayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->get();

        $yesterdayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $yesterday)
            ->get();

        $todayRevenue = $todayOrders->sum('total_amount');
        $yesterdayRevenue = $yesterdayOrders->sum('total_amount');

        $revenueChange = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : 0;

        return [
            'orders_count' => $todayOrders->count(),
            'orders_change' => $todayOrders->count() - $yesterdayOrders->count(),
            'revenue' => $todayRevenue,
            'revenue_change' => round($revenueChange, 1),
            'pending_orders' => $todayOrders->where('status', OrderStatus::Pending->value)->count(),
        ];
    }
}
