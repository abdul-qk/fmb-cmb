<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ItemCategoryResource;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryApiController extends BaseApiController
{
    /**
     * Display a listing of item categories.
     */
    public function index(Request $request)
    {
        $query = ItemCategory::query();

        // Apply filters
        $allowedFilters = ['name', 'division', 'zoho_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $categories = $query->paginate($perPage);

        return $this->paginatedResponse($categories);
    }

    /**
     * Display the specified item category.
     */
    public function show(Request $request, $id)
    {
        $category = ItemCategory::find($id);

        if (!$category) {
            return $this->errorResponse('Item category not found', 404);
        }

        return $this->successResponse(new ItemCategoryResource($category));
    }
}

