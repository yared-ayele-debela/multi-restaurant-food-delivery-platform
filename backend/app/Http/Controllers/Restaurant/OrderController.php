<?php

namespace App\Http\Controllers\Restaurant;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        // dd($request->all());
        $restaurant = $request->attributes->get('restaurant');
        // dd($restaurant->id);

        // Authorize using policy
        $this->authorize('manageOrders', $restaurant);

        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['user', 'orderItems.product', 'delivery']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        } else {
            // Default to today
            $query->whereDate('created_at', today());
        }

        // Search by order number
        if ($request->filled('q')) {
            $query->where('order_number', 'like', '%'.$request->input('q').'%');
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Group by status for the board view
        $ordersByStatus = $orders->groupBy('status');

        // Get counts for each status
        $statusCounts = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', $request->input('date', today()))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('restaurant.orders.index', [
            'orders' => $orders,
            'ordersByStatus' => $ordersByStatus,
            'statusCounts' => $statusCounts,
            'restaurant' => $restaurant,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function board(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageOrders', $restaurant);

        // Get orders grouped by status for kanban-style board
        $orders = Order::where('restaurant_id', $restaurant->id)
            ->with(['user', 'orderItems.product', 'delivery.driver.user'])
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Accepted->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
            ])
            ->latest()
            ->get();

        $pendingOrders = $orders->where('status', OrderStatus::Pending->value);
        $acceptedOrders = $orders->where('status', OrderStatus::Accepted->value);
        $preparingOrders = $orders->where('status', OrderStatus::Preparing->value);
        $readyOrders = $orders->where('status', OrderStatus::Ready->value);

        return view('restaurant.orders.board', [
            'pendingOrders' => $pendingOrders,
            'acceptedOrders' => $acceptedOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => $readyOrders,
            'restaurant' => $restaurant,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('updateOrder', $order);

        $order->load(['user', 'orderItems.product', 'delivery.driver.user', 'statusHistory.user']);

        return view('restaurant.orders.show', [
            'order' => $order,
            'restaurant' => $restaurant,
        ]);
    }

    public function accept(Request $request, Order $order): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('updateOrder', $order);

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status !== OrderStatus::Pending) {
            return back()->with('error', 'Order cannot be accepted.');
        }

        $order->update([
            'status' => OrderStatus::Accepted,
            'accepted_at' => now(),
        ]);

        $this->logStatusChange($order, OrderStatus::Pending, OrderStatus::Accepted);

        return back()->with('success', 'Order accepted successfully.');
    }

    public function markPreparing(Request $request, Order $order): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('updateOrder', $order);

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status !== OrderStatus::Accepted) {
            return back()->with('error', 'Order must be accepted before preparing.');
        }

        $order->update([
            'status' => OrderStatus::Preparing,
            'preparing_at' => now(),
        ]);

        $this->logStatusChange($order, OrderStatus::Accepted, OrderStatus::Preparing);

        return back()->with('success', 'Order is now being prepared.');
    }

    public function markReady(Request $request, Order $order): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('updateOrder', $order);

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status !== OrderStatus::Preparing) {
            return back()->with('error', 'Order must be preparing before marking as ready.');
        }

        $order->update([
            'status' => OrderStatus::Ready,
            'ready_at' => now(),
        ]);

        $this->logStatusChange($order, OrderStatus::Preparing, OrderStatus::Ready);

        return back()->with('success', 'Order is ready for pickup/delivery.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('updateOrder', $order);

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if (!in_array($order->status, [OrderStatus::Pending->value, OrderStatus::Accepted->value])) {
            return back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        $previousStatus = $order->status;

        $order->update([
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['reason'],
            'cancelled_by' => auth()->user()?->id,
        ]);

        $this->logStatusChange($order, OrderStatus::from($previousStatus), OrderStatus::Cancelled, $validated['reason']);

        return back()->with('success', 'Order cancelled.');
    }

    public function refresh(Request $request): JsonResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageOrders', $restaurant);

        $counts = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return response()->json([
            'counts' => $counts,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function authorizeAccess($restaurant, Order $order): void
    {
        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this order.');
        }
    }

    private function logStatusChange(Order $order, OrderStatus $from, OrderStatus $to, ?string $note = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $to,
            'previous_status' => $from,
            'changed_by' => auth()->user()?->id,
            'note' => $note,
        ]);
    }
}
