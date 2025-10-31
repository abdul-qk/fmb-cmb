<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApiController extends BaseApiController
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // Apply filters
        $allowedFilters = ['city_id', 'name', 'email'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'name', 'asc');

        // Apply includes
        $allowedIncludes = ['city', 'contactPerson', 'bank', 'contactPersons', 'banks', 'items'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $vendors = $query->paginate($perPage);

        return $this->paginatedResponse($vendors);
    }

    /**
     * Display the specified vendor.
     */
    public function show(Request $request, $id)
    {
        $query = Vendor::query();

        // Apply includes
        $allowedIncludes = ['city', 'contactPerson', 'bank', 'contactPersons', 'banks', 'items'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $vendor = $query->find($id);

        if (!$vendor) {
            return $this->errorResponse('Vendor not found', 404);
        }

        return $this->successResponse(new VendorResource($vendor));
    }
}

