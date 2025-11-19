<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\UserMenuSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Get dishes grouped by day for a week (Monday to Friday)
     * 
     * Query parameters:
     * - week_start: Start date of the week (YYYY-MM-DD). Defaults to current week's Monday
     * - place_id: Optional filter by place
     */
    public function dishesByWeek(Request $request)
    {
        // Get week start date from request or default to current week's Monday
        $weekStartInput = $request->get('week_start');

        if ($weekStartInput) {
            $weekStart = \Carbon\Carbon::parse($weekStartInput)->startOfWeek();
        } else {
            $weekStart = \Carbon\Carbon::now()->startOfWeek();
        }

        // Get all 5 weekdays (Monday to Friday)
        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $weekDays[] = $weekStart->copy()->addDays($i)->format('Y-m-d');
        }

        // Build base query for events
        $eventsQuery = \App\Models\Event::with([
            'menus.recipes.dish.dishCategory'
        ]);

        // Filter by date range (Monday to Friday)
        $eventsQuery->whereBetween('date', [
            $weekDays[0],
            $weekDays[4]
        ]);

        // Optional place filter
        if ($request->has('place_id')) {
            $eventsQuery->where('place_id', $request->get('place_id'));
        }

        $events = $eventsQuery->get();

        // Build response: 5 objects, one per day
        $result = [];

        foreach ($weekDays as $index => $date) {
            $dayName = \Carbon\Carbon::parse($date)->format('l'); // Monday, Tuesday, etc.

            // Get events for this specific day
            $dayEvents = $events->filter(function ($event) use ($date) {
                return $event->date === $date;
            });

            // Collect all unique dishes from menus of events on this day
            $dishes = $dayEvents->flatMap(function ($event) {
                return $event->menus->flatMap(function ($menu) use ($event) {
                    return $menu->recipes->map(function ($recipe) use ($menu, $event) {
                        return [
                            'id' => $recipe->dish->id,
                            'name' => $recipe->dish->name,
                            'category' => [
                                'id' => $recipe->dish->dishCategory->id,
                                'name' => $recipe->dish->dishCategory->name,
                            ],
                            'recipe_id' => $recipe->id,
                            'menu_id' => $menu->id,
                            'event_id' => $event->id,
                            'event_name' => $event->name,
                        ];
                    });
                });
            })->unique('id')->values(); // Remove duplicates by dish ID

            $result[] = [
                'day' => $dayName,
                'date' => $date,
                'day_number' => $index + 1, // 1 = Monday, 2 = Tuesday, etc.
                'dishes' => $dishes->toArray(),
            ];
        }

        return $this->successResponse($result, 'Dishes retrieved successfully');
    }

    /**
     * Save user menu selections for a week
     * 
     * Request body:
     * {
     *   "external_user_id": "F0020A",
     *   "selections": [
     *     {
     *       "event_id": 1,
     *       "event_date": "2025-11-10",
     *       "dish_id": 1,
     *       "quantity": 1  // 0 = skip that day, 1+ = want that many
     *     },
     *     ...
     *   ]
     * }
     */
    public function saveUserSelections(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'external_user_id' => 'required|string',
            'selections' => 'required|array',
            'selections.*.event_id' => 'required|exists:events,id',
            'selections.*.event_date' => 'required|date_format:Y-m-d',
            'selections.*.dish_id' => 'required|exists:dishes,id',
            'selections.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $externalUserId = $request->input('external_user_id');
            $selections = $request->input('selections');

            // Get event IDs from selections to group by event
            $eventIds = collect($selections)->pluck('event_id')->unique();

            // Delete existing selections for these events and this external user
            UserMenuSelection::where('external_user_id', $externalUserId)
                ->whereIn('event_id', $eventIds)
                ->delete();

            // Insert new selections
            $insertData = [];
            foreach ($selections as $selection) {
                // Only insert if quantity > 0 (if quantity is 0, we just skip it by not inserting)
                if ($selection['quantity'] > 0) {
                    $insertData[] = [
                        'external_user_id' => $externalUserId,
                        'event_id' => $selection['event_id'],
                        'event_date' => $selection['event_date'],
                        'dish_id' => $selection['dish_id'],
                        'quantity' => $selection['quantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($insertData)) {
                UserMenuSelection::insert($insertData);
            }

            DB::commit();

            return $this->successResponse([
                'external_user_id' => $externalUserId,
                'selections_saved' => count($insertData),
                'selections_skipped' => count($selections) - count($insertData),
            ], 'Menu selections saved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to save selections: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user menu selections for a week
     * 
     * Query parameters:
     * - external_user_id: External User ID like "F0020A" (required)
     * - week_start: Start date of the week (YYYY-MM-DD). Defaults to current week's Monday
     */
    public function getUserSelections(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_user_id' => 'required|string',
            'week_start' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Get week start date from request or default to current week's Monday
        $weekStartInput = $request->get('week_start');
        
        if ($weekStartInput) {
            $weekStart = \Carbon\Carbon::parse($weekStartInput)->startOfWeek();
        } else {
            $weekStart = \Carbon\Carbon::now()->startOfWeek();
        }
        
        // Get all 5 weekdays (Monday to Friday)
        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $weekDays[] = $weekStart->copy()->addDays($i)->format('Y-m-d');
        }

        // Get user selections for this week
        // Join with events to filter by event date rather than filtering events first
        $selections = UserMenuSelection::with(['event', 'dish.dishCategory'])
            ->where('external_user_id', $request->input('external_user_id'))
            ->whereHas('event', function ($query) use ($weekDays) {
                $query->whereBetween('date', [
                    $weekDays[0],
                    $weekDays[4]
                ]);
            })
            ->get()
            ->groupBy(function ($selection) {
                return $selection->event->date;
            });

        // Build response: 5 objects, one per day
        $result = [];
        
        foreach ($weekDays as $index => $date) {
            $dayName = \Carbon\Carbon::parse($date)->format('l');
            
            // Get selections for this day
            $daySelections = $selections->get($date, collect());
            
            $result[] = [
                'day' => $dayName,
                'date' => $date,
                'day_number' => $index + 1,
                'selections' => $daySelections->map(function ($selection) {
                    return [
                        'id' => $selection->id,
                        'dish_id' => $selection->dish_id,
                        'dish_name' => $selection->dish->name,
                        'dish_category' => [
                            'id' => $selection->dish->dishCategory->id,
                            'name' => $selection->dish->dishCategory->name,
                        ],
                        'event_id' => $selection->event_id,
                        'event_name' => $selection->event->name,
                        'quantity' => $selection->quantity,
                    ];
                })->values()->toArray(),
            ];
        }

        return $this->successResponse($result, 'User selections retrieved successfully');
    }

    /**
     * Fetch comprehensive menu data by eventId or event_date
     * 
     * Query parameters:
     * - event_id: Event ID (optional)
     * - event_date: Event date in YYYY-MM-DD format (optional)
     * 
     * At least one of event_id or event_date must be provided
     */
    public function fetchMenuData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,id',
            'event_date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // At least one parameter must be provided
        if (!$request->has('event_id') && !$request->has('event_date')) {
            return $this->errorResponse('Either event_id or event_date must be provided', 422);
        }

        // Build query to get menus with all relationships
        $query = Menu::with([
            'event',
            'recipes' => function ($q) {
                $q->with([
                    'dish.dishCategory',
                    'recipeItems' => function ($q) {
                        $q->with([
                            'item.itemCategory',
                            'measurement'
                        ]);
                    },
                    'place'
                ]);
            },
            'menuServings' => function ($q) {
                $q->with([
                    'recipeItem' => function ($q) {
                        $q->with([
                            'item.itemCategory',
                            'measurement'
                        ]);
                    }
                ]);
            }
        ]);

        // Apply filters based on event_id or event_date
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        if ($request->has('event_date')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('date', $request->input('event_date'));
            });
        }

        // Get menus
        $menus = $query->get();

        if ($menus->isEmpty()) {
            return $this->successResponse([], 'No menus found for the specified criteria');
        }

        // Format the response
        $formattedMenus = $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'event_id' => $menu->event_id,
                'item_quantity' => $menu->item_quantity,
                'description' => $menu->description,
                'event' => $menu->event ? [
                    'id' => $menu->event->id,
                    'name' => $menu->event->name,
                    'date' => $menu->event->date ? (
                        $menu->event->date instanceof \Carbon\Carbon 
                            ? $menu->event->date->format('Y-m-d')
                            : \Carbon\Carbon::parse($menu->event->date)->format('Y-m-d')
                    ) : null,
                    'start' => $menu->event->start,
                    'end' => $menu->event->end,
                    'meal' => $menu->event->meal,
                    'serving' => $menu->event->serving,
                    'serving_persons' => $menu->event->serving_persons,
                    'no_of_thaal' => $menu->event->no_of_thaal,
                    'status' => $menu->event->status,
                ] : null,
                'recipes' => $menu->recipes->map(function ($recipe) {
                    return [
                        'id' => $recipe->id,
                        'dish' => $recipe->dish ? [
                            'id' => $recipe->dish->id,
                            'name' => $recipe->dish->name,
                            'category' => $recipe->dish->dishCategory ? [
                                'id' => $recipe->dish->dishCategory->id,
                                'name' => $recipe->dish->dishCategory->name,
                            ] : null,
                        ] : null,
                        'chef' => $recipe->chef,
                        'serving' => $recipe->serving,
                        'serving_item' => $recipe->serving_item,
                        'place' => $recipe->place ? [
                            'id' => $recipe->place->id,
                            'name' => $recipe->place->name,
                        ] : null,
                        'recipe_items' => $recipe->recipeItems->map(function ($recipeItem) {
                            return [
                                'id' => $recipeItem->id,
                                'item' => $recipeItem->item ? [
                                    'id' => $recipeItem->item->id,
                                    'name' => $recipeItem->item->name,
                                    'category' => $recipeItem->item->itemCategory ? [
                                        'id' => $recipeItem->item->itemCategory->id,
                                        'name' => $recipeItem->item->itemCategory->name,
                                    ] : null,
                                ] : null,
                                'item_quantity' => $recipeItem->item_quantity,
                                'select_item_quantity' => $recipeItem->select_item_quantity,
                                'measurement' => $recipeItem->measurement ? [
                                    'id' => $recipeItem->measurement->id,
                                    'name' => $recipeItem->measurement->name,
                                    'short_form' => $recipeItem->measurement->short_form,
                                ] : null,
                                'select_measurement_id' => $recipeItem->select_measurement_id,
                                'description' => $recipeItem->description,
                            ];
                        }),
                    ];
                }),
                'menu_servings' => $menu->menuServings->map(function ($menuServing) {
                    return [
                        'id' => $menuServing->id,
                        'recipe_item_id' => $menuServing->recipe_item_id,
                        'per_person_quantity' => $menuServing->per_person_quantity,
                        'total_quantity' => $menuServing->total_quantity,
                        'recipe_item' => $menuServing->recipeItem ? [
                            'id' => $menuServing->recipeItem->id,
                            'item' => $menuServing->recipeItem->item ? [
                                'id' => $menuServing->recipeItem->item->id,
                                'name' => $menuServing->recipeItem->item->name,
                                'category' => $menuServing->recipeItem->item->itemCategory ? [
                                    'id' => $menuServing->recipeItem->item->itemCategory->id,
                                    'name' => $menuServing->recipeItem->item->itemCategory->name,
                                ] : null,
                            ] : null,
                            'item_quantity' => $menuServing->recipeItem->item_quantity,
                            'measurement' => $menuServing->recipeItem->measurement ? [
                                'id' => $menuServing->recipeItem->measurement->id,
                                'name' => $menuServing->recipeItem->measurement->name,
                                'short_form' => $menuServing->recipeItem->measurement->short_form,
                            ] : null,
                        ] : null,
                    ];
                }),
                'created_at' => $menu->created_at?->toIso8601String(),
                'updated_at' => $menu->updated_at?->toIso8601String(),
            ];
        });

        return $this->successResponse($formattedMenus, 'Menu data retrieved successfully');
    }
}
