<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeApiController extends BaseApiController
{
    /**
     * Display a listing of recipes.
     */
    public function index(Request $request)
    {
        $query = Recipe::query();

        // Apply filters
        $allowedFilters = ['place_id', 'dish_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['dish', 'place', 'recipeItems'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $recipes = $query->paginate($perPage);

        return $this->paginatedResponse($recipes);
    }

    /**
     * Display the specified recipe.
     */
    public function show(Request $request, $id)
    {
        $query = Recipe::query();

        // Apply includes
        $allowedIncludes = ['dish', 'place', 'recipeItems'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $recipe = $query->find($id);

        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        return $this->successResponse(new RecipeResource($recipe));
    }
}

