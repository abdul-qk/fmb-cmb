<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuApiController extends BaseApiController
{
    /**
     * Display a listing of menus.
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        // Apply filters
        $allowedFilters = ['event_id'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['event'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $menus = $query->paginate($perPage);

        return $this->paginatedResponse($menus);
    }

    /**
     * Display the specified menu.
     */
    public function show(Request $request, $id)
    {
        $query = Menu::query();

        // Apply includes
        $allowedIncludes = ['event'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $menu = $query->find($id);

        if (!$menu) {
            return $this->errorResponse('Menu not found', 404);
        }

        return $this->successResponse(new MenuResource($menu));
    }
}

