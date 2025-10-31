<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceApiController extends BaseApiController
{
    /**
     * Display a listing of places.
     */
    public function index(Request $request)
    {
        $query = Place::query();

        // Apply filters
        $allowedFilters = ['location_id', 'incharge_id', 'default'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Apply includes
        $allowedIncludes = ['location', 'incharge'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $places = $query->paginate($perPage);

        return $this->paginatedResponse($places);
    }

    /**
     * Display the specified place.
     */
    public function show(Request $request, $id)
    {
        $query = Place::query();

        // Apply includes
        $allowedIncludes = ['location', 'incharge'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $place = $query->find($id);

        if (!$place) {
            return $this->errorResponse('Place not found', 404);
        }

        return $this->successResponse(new PlaceResource($place));
    }
}

