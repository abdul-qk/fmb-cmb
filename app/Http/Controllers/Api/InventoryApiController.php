<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryApiController extends BaseApiController
{
    /**
     * Display a listing of inventories.
     */
    public function index(Request $request)
    {
        $query = Inventory::query();

        // Apply filters
        $allowedFilters = ['store_id', 'inventory_status', 'approved_purchase_order_detail_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['store'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $inventories = $query->paginate($perPage);

        return $this->paginatedResponse($inventories);
    }

    /**
     * Display the specified inventory.
     */
    public function show(Request $request, $id)
    {
        $query = Inventory::query();

        // Apply includes
        $allowedIncludes = ['store'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $inventory = $query->find($id);

        if (!$inventory) {
            return $this->errorResponse('Inventory not found', 404);
        }

        return $this->successResponse(new InventoryResource($inventory));
    }
}

