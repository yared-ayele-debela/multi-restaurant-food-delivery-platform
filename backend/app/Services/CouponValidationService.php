<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CouponValidationService
{
    /**
     * Resolve and validate a coupon; compute item and delivery discounts (no DB writes).
     *
     * @return array{coupon: Coupon, item_discount: float, delivery_discount: float, delivery_fee_charged: float, total_discount: float}
     */
    public function validateForCheckout(
        User $user,
        Restaurant $restaurant,
        ?string $code,
        float $itemsSubtotal,
        float $baseDeliveryFee
    ): array {
        if ($code === null || trim($code) === '') {
            return $this->emptyResult($baseDeliveryFee);
        }

        $coupon = Coupon::query()
            ->whereRaw('LOWER(code) = ?', [strtolower(trim($code))])
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon code is not valid.'],
            ]);
        }

        if (! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is not active.'],
            ]);
        }

        if ($coupon->restaurant_id !== null && (int) $coupon->restaurant_id !== (int) $restaurant->id) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon does not apply to this restaurant.'],
            ]);
        }

        $now = Carbon::now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is not valid yet.'],
            ]);
        }

        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon has expired.'],
            ]);
        }

        if ((float) $coupon->min_order_amount > 0 && $itemsSubtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => ['Minimum order amount not met for this coupon.'],
            ]);
        }

        if ($coupon->is_first_order_only && Order::query()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is for first orders only.'],
            ]);
        }

        if ($coupon->max_uses !== null && $coupon->current_uses >= $coupon->max_uses) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is no longer available.'],
            ]);
        }

        $perUserLimit = $coupon->max_uses_per_user;
        if ($perUserLimit !== null) {
            $used = CouponUsage::query()
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();
            if ($used >= $perUserLimit) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['You have already used this coupon the maximum number of times.'],
                ]);
            }
        }

        $itemDiscount = 0.0;
        $deliveryDiscount = 0.0;

        if ($coupon->type === 'free_delivery') {
            $deliveryDiscount = round($baseDeliveryFee, 2);
        } elseif ($coupon->type === 'percentage') {
            $pct = (float) $coupon->value;
            $raw = round($itemsSubtotal * ($pct / 100), 2);
            $cap = $coupon->max_discount_amount !== null ? (float) $coupon->max_discount_amount : $raw;
            $itemDiscount = round(min($raw, $cap), 2);
        } elseif ($coupon->type === 'fixed') {
            $itemDiscount = round(min((float) $coupon->value, $itemsSubtotal), 2);
        }

        $deliveryFeeCharged = round(max(0, $baseDeliveryFee - $deliveryDiscount), 2);
        $totalDiscount = round($itemDiscount + $deliveryDiscount, 2);

        return [
            'coupon' => $coupon,
            'item_discount' => $itemDiscount,
            'delivery_discount' => $deliveryDiscount,
            'delivery_fee_charged' => $deliveryFeeCharged,
            'total_discount' => $totalDiscount,
        ];
    }

    /**
     * Increment usage counters and record coupon_usages (call inside order transaction after order exists).
     */
    public function recordUsage(Coupon $coupon, User $user, Order $order, float $discountAmount): void
    {
        $coupon = Coupon::query()->whereKey($coupon->id)->lockForUpdate()->firstOrFail();
        $coupon->increment('current_uses');

        CouponUsage::query()->create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => round($discountAmount, 2),
            'created_at' => now(),
        ]);
    }

    /**
     * @return array{coupon: null, item_discount: float, delivery_discount: float, delivery_fee_charged: float, total_discount: float}
     */
    private function emptyResult(float $baseDeliveryFee): array
    {
        return [
            'coupon' => null,
            'item_discount' => 0.0,
            'delivery_discount' => 0.0,
            'delivery_fee_charged' => round($baseDeliveryFee, 2),
            'total_discount' => 0.0,
        ];
    }
}
