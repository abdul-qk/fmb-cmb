<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\KitchenResource;
use App\Models\Kitchen;
use Illuminate\Http\Request;

class KitchenApiController extends BaseApiController
{
    /**
     * Display a listing of kitchens.
     */
    public function index(Request $request)
    {
        $query = Kitchen::query();

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
        $kitchens = $query->paginate($perPage);

        return $this->paginatedResponse($kitchens);
    }

    /**
     * Display the specified kitchen.
     */
    public function show(Request $request, $id)
    {
        $query = Kitchen::query();

        // Apply includes
        $allowedIncludes = ['place'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $kitchen = $query->find($id);

        if (!$kitchen) {
            return $this->errorResponse('Kitchen not found', 404);
        }

        return $this->successResponse(new KitchenResource($kitchen));
    }
}

