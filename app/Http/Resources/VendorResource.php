<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'zoho_vendor_number' => $this->zoho_vendor_number,
            'name' => $this->name,
            'email' => $this->email,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'city' => $this->whenLoaded('city', fn() => new CityResource($this->city)),
            'contact_person' => $this->whenLoaded('contactPerson'),
            'bank' => $this->whenLoaded('bank'),
            'contact_persons' => $this->whenLoaded('contactPersons'),
            'banks' => $this->whenLoaded('banks'),
            'items' => $this->whenLoaded('items', fn() => ItemResource::collection($this->items)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

