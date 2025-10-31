<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderApiController extends BaseApiController
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        // Apply filters
        $allowedFilters = ['vendor_id', 'place_id', 'store_id', 'status', 'type', 'menu_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['vendor', 'place', 'store', 'menu', 'currency', 'detail', 'events'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $purchaseOrders = $query->paginate($perPage);

        return $this->paginatedResponse($purchaseOrders);
    }

    /**
     * Display the specified purchase order.
     */
    public function show(Request $request, $id)
    {
        $query = PurchaseOrder::query();

        // Apply includes
        $allowedIncludes = ['vendor', 'place', 'store', 'menu', 'currency', 'detail', 'events'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $purchaseOrder = $query->find($id);

        if (!$purchaseOrder) {
            return $this->errorResponse('Purchase order not found', 404);
        }

        return $this->successResponse(new PurchaseOrderResource($purchaseOrder));
    }
}

