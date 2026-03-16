<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RestaurantAssignDriverRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderDeliveryService;
use App\Services\OrderStatusTransitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantOrderController extends Controller
{
    public function __construct(
        private OrderStatusTransitionService $orderStatusTransitionService,
        private OrderDeliveryService $orderDeliveryService,
    ) {}

    public function accept(Request $request, Order $order): JsonResponse
    {
        $this->authorize('manageRestaurantOrder', $order);

        $order = $this->orderStatusTransitionService->transition(
            $order,
            OrderStatus::Accepted,
            $request->user(),
        );

        return (new OrderResource($order->load(['restaurant', 'orderItems', 'delivery'])))->response();
    }

    public function preparing(Request $request, Order $order): JsonResponse
    {
        $this->authorize('manageRestaurantOrder', $order);

        $order = $this->orderStatusTransitionService->transition(
            $order,
            OrderStatus::Preparing,
            $request->user(),
        );

        return (new OrderResource($order->load(['restaurant', 'orderItems', 'delivery'])))->response();
    }

    public function ready(Request $request, Order $order): JsonResponse
    {
        $this->authorize('manageRestaurantOrder', $order);

        $order = $this->orderStatusTransitionService->transition(
            $order,
            OrderStatus::Ready,
            $request->user(),
        );

        return (new OrderResource($order->load(['restaurant', 'orderItems', 'delivery'])))->response();
    }

    public function assignDriver(RestaurantAssignDriverRequest $request, Order $order): JsonResponse
    {
        $this->authorize('manageRestaurantOrder', $order);

        $this->orderDeliveryService->assignDriver($order, (int) $request->validated('driver_id'));

        return (new OrderResource($order->fresh()->load(['restaurant', 'orderItems', 'delivery.driver.user'])))->response();
    }
}
