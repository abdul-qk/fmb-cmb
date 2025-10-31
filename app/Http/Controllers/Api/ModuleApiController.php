<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleApiController extends BaseApiController
{
    /**
     * Display a listing of modules.
     */
    public function index(Request $request)
    {
        $query = Module::query();

        // Apply filters
        $allowedFilters = ['is_active', 'parent_id', 'slug'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'display_order', 'asc');

        // Apply includes
        $allowedIncludes = ['parent', 'children', 'permissions'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $modules = $query->paginate($perPage);

        return $this->paginatedResponse($modules);
    }

    /**
     * Display the specified module.
     */
    public function show(Request $request, $id)
    {
        $query = Module::query();

        // Apply includes
        $allowedIncludes = ['parent', 'children', 'permissions'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $module = $query->find($id);

        if (!$module) {
            return $this->errorResponse('Module not found', 404);
        }

        return $this->successResponse(new ModuleResource($module));
    }
}

