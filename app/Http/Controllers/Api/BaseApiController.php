<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="ERP FMB API",
 *     version="1.0.0",
 *     description="API documentation for ERP FMB system - Read-only endpoints for accessing database entities"
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class BaseApiController extends Controller
{
    /**
     * Success JSON response
     */
    protected function successResponse($data, $message = null, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Error JSON response
     */
    protected function errorResponse($message, $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Paginated JSON response
     */
    protected function paginatedResponse($paginator, $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, Request $request, array $allowedFilters = []): void
    {
        foreach ($allowedFilters as $filter) {
            if ($request->has($filter)) {
                $value = $request->get($filter);
                
                // Handle date range filters
                if (str_contains($filter, '_from') || str_contains($filter, '_to')) {
                    $field = str_replace(['_from', '_to'], '', $filter);
                    if (str_contains($filter, '_from')) {
                        $query->where($field, '>=', $value);
                    } else {
                        $query->where($field, '<=', $value);
                    }
                }
                // Handle exact match filters
                else {
                    $query->where($filter, $value);
                }
            }
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, Request $request, $defaultSort = 'id', $defaultOrder = 'desc'): void
    {
        $sort = $request->get('sort', $defaultSort);
        $order = strtolower($request->get('order', $defaultOrder)) === 'asc' ? 'asc' : 'desc';
        
        $query->orderBy($sort, $order);
    }

    /**
     * Apply includes (eager loading)
     */
    protected function applyIncludes($query, Request $request, array $allowedIncludes = []): void
    {
        if ($request->has('include')) {
            $includes = explode(',', $request->get('include'));
            $includes = array_intersect($includes, $allowedIncludes);
            
            if (!empty($includes)) {
                $query->with($includes);
            }
        }
    }

    /**
     * Get pagination per page
     */
    protected function getPerPage(Request $request, $default = 15): int
    {
        $perPage = (int) $request->get('per_page', $default);
        
        // Limit max per page to prevent resource exhaustion
        return min(max($perPage, 1), 100);
    }

    /**
     * Validate request data
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array|bool
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }
        
        return true;
    }
}
