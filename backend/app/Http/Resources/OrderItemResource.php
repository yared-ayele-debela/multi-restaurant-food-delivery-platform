<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\OrderItem */
class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_size_id' => $this->product_size_id,
            'product_name' => $this->product_name,
            'product_size_name' => $this->product_size_name,
            'item_name' => $this->item_name,
            'unit_price' => (string) $this->unit_price,
            'quantity' => $this->quantity,
            'addons' => $this->addons,
            'addons_total' => (string) $this->addons_total,
            'subtotal' => (string) $this->subtotal,
        ];
    }
}
