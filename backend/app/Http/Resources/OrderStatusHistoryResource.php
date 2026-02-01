<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\OrderStatusHistory */
class OrderStatusHistoryResource extends JsonResource
{
    /**
     * Customer-safe timeline (no internal notes or raw user ids).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'previous_status' => $this->previous_status,
            'new_status' => $this->new_status,
            'changed_at' => $this->created_at?->toIso8601String(),
            'actor' => $this->when(
                $this->relationLoaded('changedBy') && $this->changedBy,
                fn () => [
                    'name' => $this->changedBy->name,
                ]
            ),
        ];
    }
}
