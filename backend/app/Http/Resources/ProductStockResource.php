<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ProductStock */
class ProductStockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'quantity' => $this->quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'track_stock' => $this->track_stock,
        ];
    }
}
