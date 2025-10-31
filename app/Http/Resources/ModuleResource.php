<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            'slug' => $this->slug,
            'icon' => $this->icon,
            'display_order' => $this->display_order,
            'is_active' => (bool) $this->is_active,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn() => new ModuleResource($this->parent)),
            'children' => $this->whenLoaded('children', fn() => ModuleResource::collection($this->children)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

