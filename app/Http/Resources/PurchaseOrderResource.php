<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'place_id' => $this->place_id,
            'store_id' => $this->store_id,
            'menu_id' => $this->menu_id,
            'currency_id' => $this->currency_id,
            'incharge_id' => $this->incharge_id,
            'amount' => $this->amount,
            'sub_amount' => $this->sub_amount,
            'additional_charges' => $this->additional_charges,
            'discount' => $this->discount,
            'status' => $this->status,
            'type' => $this->type,
            'description' => $this->description,
            'delivery_date' => $this->delivery_date?->toIso8601String(),
            'grn_date' => $this->grn_date?->format('Y-m-d'),
            'bill_no' => $this->bill_no,
            'vendor' => $this->whenLoaded('vendor', fn() => new VendorResource($this->vendor)),
            'place' => $this->whenLoaded('place', fn() => new PlaceResource($this->place)),
            'store' => $this->whenLoaded('store', fn() => new StoreResource($this->store)),
            'menu' => $this->whenLoaded('menu', fn() => new MenuResource($this->menu)),
            'currency' => $this->whenLoaded('currency'),
            'details' => $this->whenLoaded('detail'),
            'events' => $this->whenLoaded('events', fn() => EventResource::collection($this->events)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

