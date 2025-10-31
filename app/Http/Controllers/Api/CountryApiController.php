<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryApiController extends BaseApiController
{
    /**
     * Display a listing of countries.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Apply filters
        $allowedFilters = ['code', 'name'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $countries = $query->paginate($perPage);

        return $this->paginatedResponse($countries);
    }

    /**
     * Display the specified country.
     */
    public function show(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->errorResponse('Country not found', 404);
        }

        return $this->successResponse(new CountryResource($country));
    }
}

