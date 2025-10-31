<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'place_id' => $this->place_id,
            'date' => $this->date?->format('Y-m-d'),
            'start' => $this->start,
            'end' => $this->end,
            'event_hours' => $this->event_hours,
            'meal' => $this->meal,
            'serving' => $this->serving,
            'serving_persons' => $this->serving_persons,
            'no_of_thaal' => $this->no_of_thaal,
            'actual_thaal' => $this->actual_thaal,
            'status' => $this->status,
            'description' => $this->description,
            'place' => $this->whenLoaded('place', fn() => new PlaceResource($this->place)),
            'menus' => $this->whenLoaded('menus', fn() => MenuResource::collection($this->menus)),
            'menu' => $this->whenLoaded('menu', fn() => new MenuResource($this->menu)),
            'purchase_orders' => $this->whenLoaded('purchaseOrder', fn() => PurchaseOrderResource::collection($this->purchaseOrder)),
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'rejected_by' => $this->rejected_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

