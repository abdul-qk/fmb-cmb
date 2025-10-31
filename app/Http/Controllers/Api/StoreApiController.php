<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreApiController extends BaseApiController
{
    /**
     * Display a listing of stores.
     */
    public function index(Request $request)
    {
        $query = Store::query();

        // Apply filters
        $allowedFilters = ['place_id', 'default'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['place'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $stores = $query->paginate($perPage);

        return $this->paginatedResponse($stores);
    }

    /**
     * Display the specified store.
     */
    public function show(Request $request, $id)
    {
        $query = Store::query();

        // Apply includes
        $allowedIncludes = ['place'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $store = $query->find($id);

        if (!$store) {
            return $this->errorResponse('Store not found', 404);
        }

        return $this->successResponse(new StoreResource($store));
    }
}

