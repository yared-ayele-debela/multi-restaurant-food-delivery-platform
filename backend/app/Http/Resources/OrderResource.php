<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status?->value,
            'subtotal' => (string) $this->subtotal,
            'discount_amount' => (string) $this->discount_amount,
            'delivery_fee' => (string) $this->delivery_fee,
            'tax_amount' => (string) $this->tax_amount,
            'tax_rate' => (string) $this->tax_rate,
            'total' => (string) $this->total,
            'coupon_id' => $this->coupon_id,
            'loyalty_points_earned' => $this->loyalty_points_earned,
            'loyalty_points_redeemed' => $this->loyalty_points_redeemed,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'delivery_address' => $this->delivery_address,
            'delivery_notes' => $this->delivery_notes,
            'placed_at' => $this->placed_at?->toIso8601String(),
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'preparing_at' => $this->preparing_at?->toIso8601String(),
            'ready_at' => $this->ready_at?->toIso8601String(),
            'picked_up_at' => $this->picked_up_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $this->cancellation_reason,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'delivery' => $this->when(
                $this->relationLoaded('delivery') && $this->delivery,
                fn () => new DeliveryResource($this->delivery)
            ),
            'status_history' => OrderStatusHistoryResource::collection($this->whenLoaded('statusHistory')),
        ];
    }
}
