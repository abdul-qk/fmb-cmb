<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemApiController extends BaseApiController
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        // Apply filters
        $allowedFilters = ['category_id', 'name', 'zoho_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Apply includes
        $allowedIncludes = ['itemCategory', 'vendors', 'itemBase', 'detail'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $items = $query->paginate($perPage);

        return $this->paginatedResponse($items);
    }

    /**
     * Display the specified item.
     */
    public function show(Request $request, $id)
    {
        $query = Item::query();

        // Apply includes
        $allowedIncludes = ['itemCategory', 'vendors', 'itemBase', 'detail'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $item = $query->find($id);

        if (!$item) {
            return $this->errorResponse('Item not found', 404);
        }

        return $this->successResponse(new ItemResource($item));
    }
}

