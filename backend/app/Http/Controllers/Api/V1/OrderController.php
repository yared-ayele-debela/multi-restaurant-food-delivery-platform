<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private OrderCheckoutService $orderCheckoutService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->with(['restaurant', 'orderItems'])
            ->latest('placed_at')
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderCheckoutService->placeOrder($request->user(), $request->validated());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load([
            'restaurant',
            'orderItems',
            'delivery',
            'statusHistory.changedBy',
        ]));
    }
}
