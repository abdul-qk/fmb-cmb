<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends BaseApiController
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        $allowedFilters = ['place_id', 'email'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'id', 'desc');

        // Apply includes
        $allowedIncludes = ['place', 'roles', 'profile'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $users = $query->paginate($perPage);

        return $this->paginatedResponse($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Relationships to include",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        $query = User::query();

        // Apply includes
        $allowedIncludes = ['place', 'roles', 'profile'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $user = $query->find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse(new UserResource($user));
    }
}

