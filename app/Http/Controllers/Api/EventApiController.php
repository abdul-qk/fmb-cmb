<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventApiController extends BaseApiController
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Apply filters
        $allowedFilters = ['place_id', 'status', 'date', 'date_from', 'date_to'];
        $this->applyFilters($query, $request, $allowedFilters);

        // Apply sorting
        $this->applySorting($query, $request, 'date', 'desc');

        // Apply includes
        $allowedIncludes = ['place', 'menus', 'menu', 'purchaseOrder'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        // Paginate
        $perPage = $this->getPerPage($request, 15);
        $events = $query->paginate($perPage);

        return $this->paginatedResponse($events);
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, $id)
    {
        $query = Event::query();

        // Apply includes
        $allowedIncludes = ['place', 'menus', 'menu', 'purchaseOrder'];
        $this->applyIncludes($query, $request, $allowedIncludes);

        $event = $query->find($id);

        if (!$event) {
            return $this->errorResponse('Event not found', 404);
        }

        return $this->successResponse(new EventResource($event));
    }
}

