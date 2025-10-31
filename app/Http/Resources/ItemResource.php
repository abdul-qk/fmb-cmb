<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'zoho_id' => $this->zoho_id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'ref_rate' => $this->ref_rate,
            'category' => $this->whenLoaded('itemCategory', fn() => new ItemCategoryResource($this->itemCategory)),
            'vendors' => $this->whenLoaded('vendors', fn() => VendorResource::collection($this->vendors)),
            'item_base' => $this->whenLoaded('itemBase'),
            'detail' => $this->whenLoaded('detail'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

