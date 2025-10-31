<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityApiController extends BaseApiController
{
    /**
     * Display a listing of cities.
     */
    public function index(Request $request)
    {
        $query = City::query();

        // Apply filters
        $allowedFilters = ['country_id', 'name'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Apply includes
        $allowedIncludes = ['country'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $cities = $query->paginate($perPage);

        return $this->paginatedResponse($cities);
    }

    /**
     * Display the specified city.
     */
    public function show(Request $request, $id)
    {
        $query = City::query();

        // Apply includes
        $allowedIncludes = ['country'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $city = $query->find($id);

        if (!$city) {
            return $this->errorResponse('City not found', 404);
        }

        return $this->successResponse(new CityResource($city));
    }
}

