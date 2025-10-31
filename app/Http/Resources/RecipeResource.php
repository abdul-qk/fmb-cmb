<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'dish_id' => $this->dish_id,
            'place_id' => $this->place_id,
            'chef' => $this->chef,
            'serving_item' => $this->serving_item,
            'serving' => $this->serving,
            'dish' => $this->whenLoaded('dish'),
            'place' => $this->whenLoaded('place', fn() => new PlaceResource($this->place)),
            'recipe_items' => $this->whenLoaded('recipeItems'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

