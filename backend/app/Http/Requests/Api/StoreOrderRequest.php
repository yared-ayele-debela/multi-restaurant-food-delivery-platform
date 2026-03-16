<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'branch_id' => ['nullable', 'integer', 'exists:restaurant_branches,id'],
            'address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'delivery_address' => ['required', 'string', 'max:2000'],
            'delivery_notes' => ['nullable', 'string', 'max:2000'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'redeem_loyalty_points' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'payment_method' => ['nullable', 'string', 'in:card,wallet,cash'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_size_id' => ['nullable', 'integer', 'exists:product_sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.addons' => ['nullable', 'array'],
            'items.*.addons.*.product_addon_id' => ['required', 'integer', 'exists:product_addons,id'],
            'items.*.addons.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
