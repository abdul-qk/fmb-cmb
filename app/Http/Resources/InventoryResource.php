<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'approved_purchase_order_detail_id' => $this->approved_purchase_order_detail_id,
            'store_id' => $this->store_id,
            'quantity' => $this->quantity,
            'remaining' => $this->remaining,
            'inventory_status' => $this->inventory_status,
            'store' => $this->whenLoaded('store', fn() => new StoreResource($this->store)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

