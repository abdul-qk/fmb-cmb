<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'place_id' => $this->place_id,
            'place' => $this->whenLoaded('place', fn() => new PlaceResource($this->place)),
            'roles' => $this->whenLoaded('roles', fn() => RoleResource::collection($this->roles)),
            'first_role' => $this->when($this->relationLoaded('roles'), fn() => $this->first_role),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

