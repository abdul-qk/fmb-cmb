<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleApiController extends BaseApiController
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Apply filters
        $allowedFilters = ['name', 'guard_name'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['permissions'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $roles = $query->paginate($perPage);

        return $this->paginatedResponse($roles);
    }

    /**
     * Display the specified role.
     */
    public function show(Request $request, $id)
    {
        $query = Role::query();

        // Apply includes
        $allowedIncludes = ['permissions'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $role = $query->find($id);

        if (!$role) {
            return $this->errorResponse('Role not found', 404);
        }

        return $this->successResponse(new RoleResource($role));
    }
}

