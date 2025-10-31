<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'contact_no' => $this->contact_no,
            'location_id' => $this->location_id,
            'incharge_id' => $this->incharge_id,
            'default' => $this->default,
            'location' => $this->whenLoaded('location'),
            'incharge' => $this->whenLoaded('incharge', fn() => new UserResource($this->incharge)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

