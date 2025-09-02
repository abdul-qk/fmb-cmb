<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\AddRequest;
use App\Http\Requests\Event\MonthlyRequest;
use App\Http\Requests\Event\StoreRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\ChefRecipeItem;
use App\Models\Event;
use App\Models\Item;
use App\Models\ItemBaseUom;
use App\Models\Menu;
use App\Models\MenuServing;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Place;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ServingItem;
use App\Models\ServingQuantity;
use App\Models\ServingQuantityTiffin;
use App\Models\TiffinSize;
use App\Models\UomConversion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\isEmpty;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $events = Event::with('menu', 'place', 'servingItems.tiffinSize', 'createdBy', 'updatedBy')->get();
   
    $results = $events->map(function ($event) {
      return [
        'event' => $event->toArray(),
        'menu' => $event->menu,
        'serving_items' => $event->servingItems->map(function ($servingItem) {
          return [
            'count' => $servingItem->count,
            'tiffin_size_name' => $servingItem->tiffinSize->name,
            'tiffin_size_person_no' => $servingItem->tiffinSize->person_no,
          ];
        }),
      ];
    });

    // return $filteredEvents->toArray();
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $results = TiffinSize::get();
    $places = Place::get();
    return view($this->view, compact('results', 'places'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  // public function store(Request $request)
  {
    // dd($request->all());
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $validated["created_by"] = Auth::id();

      $hasAccess = $this->accessPermission("auto-approve");
      if ($hasAccess) {
        $validated["status"] = "Approved";
        $validated["approved_by"] = Auth::id();
      }

      $validated["updated_at"] = null;
      $eventId = Event::create($validated);
      $tiffanTypes = $request->input('tiffan_type');
      $noOfTaffins = $request->input('no_of_taffin');


      if (isset($tiffanTypes)) {
        foreach ($tiffanTypes as $index => $tiffanTypeId) {
          ServingItem::create([
            'tiffin_size_id' => $tiffanTypeId,
            'count' => $noOfTaffins[$index], // Use the same index to get the count
            'event_id' => $eventId->id, // Assuming event_id is passed from the request
          ]);
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      $result = Event::with('createdBy','updatedBy','menu')->findOrFail($id);
      $servingItems = ServingItem::where('event_id', $result->id)->get();
      return view($this->view, compact('result', 'tiffinSizes', 'servingItems', 'places'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {

    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      // $result = Event::with(['servingItem','servingItem.getTiffinSize'])->findOrFail($id);
      // dd($result->toArray());
      $result = Event::findOrFail($id);
      $servingItems = ServingItem::where('event_id', $result->id)->get();
      return view($this->view, compact('result', 'tiffinSizes', 'servingItems', 'places'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateRequest $request, string $id)
  {

    $validated = $request->validated();
    $vendorData = Arr::only($validated, ['name', 'date', 'start', 'end', 'event_hours', 'meal', 'description', 'serving', 'serving_persons', 'no_of_thaal','host_its_no','host_sabeel_no','host_name','host_menu']);
    $vendorData["updated_by"] = Auth::id();
    try {
      DB::beginTransaction();
      Event::updateWithTransaction($id, $vendorData);

      $tiffanTypes = $request->input('tiffan_type');
      $noOfTaffins = $request->input('no_of_taffin');
      $serving_item = $request->input('serving_item');
      // dd($tiffanTypes,$noOfTaffins,$serving_item);
      if (isset($tiffanTypes)) {
        foreach ($tiffanTypes as $index => $tiffanTypeId) {
          ServingItem::updateOrCreate(
            [
              'id' => isset($serving_item[$index]) ? $serving_item[$index] : null,
            ],
            [
              'tiffin_size_id' => $tiffanTypeId,
              'count' => $noOfTaffins[$index], // Use the same index to get the count
              'event_id' => $id, // Assuming event_id is passed from the request
            ]
          );
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      Event::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete ' . $this->controller . ': ' . $e->getMessage());
    }
  }
  public function approveEvent(string $id)
  {
    try {
      Event::updateWithTransaction($id, ['status' => 'approved', 'approved_by' => Auth::id()]);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' approved successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to approved ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function createMenu(string $id)
  {
    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      $result = Event::findOrFail($id);
      $servingItems = ServingItem::where('event_id', $result->id)->get();

      $results = Recipe::with('dish', 'chefUser')->get();
      $events = Event::whereDoesntHave('menus')->where("status", "Approved")->where('id', $id)->get();

      return view($this->view, compact('result', 'tiffinSizes', 'servingItems', 'places','events','results'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function storeMenu(StoreRequest $request)
  {
    try {
      $validated = $request->validated();
      $dishes = Arr::get($validated, 'dish_id', []);
      $event_id = $request['event_id'];
      $event = Event::find($event_id);
      if (!$event) {
        redirect()->back()->with('error', 'Event not found');
      }
      $menu = Menu::createWithTransaction([
        "event_id" => $event_id,
        "item_quantity" => 'recipe',
        "description" => $request->description
      ]);
      foreach ($dishes as $dish_id) {
        $menu->recipes()->attach($dish_id);
        $recipe = Recipe::where("id", $dish_id)->first();
        if ($recipe) {
          $recipeItems = RecipeItem::where("recipe_id", $recipe->id)->get();

          foreach ($recipeItems as $recipeItem) {
            
            MenuServing::create([
              "menu_id" => $menu->id,
              "recipe_item_id" => $recipeItem->id,
              "per_person_quantity" =>  ($recipeItem->item_quantity > 0) ? ($recipe->serving / $event->serving_persons) : 0,
              "total_quantity" => $recipeItem->item_quantity * ($recipe->serving / $event->serving_persons),
            ]);

            // auto approved
            $purchaseOrder = PurchaseOrder::createWithTransaction([
              "place_id" => $event->place_id,
              "status" => "approved",
              "approved_by" => Auth::id(),
              "menu_id" => $menu->id,
              'type' => 'menu'
            ]);
    
            $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
              'purchase_order_id' => $purchaseOrder->id,
              'event_id' => $event_id,
              'recipe_id' => $recipe->id,
              'item_id' => $recipeItem->item_id,
              'unit_measure_id' => $recipeItem->measurement_id,
              'select_unit_measure_id' => $recipeItem->select_measurement_id,
              // 'select_quantity' => $recipeItem->select_item_quantity,
              // 'quantity' => $recipeItem->item_quantity,
              'select_quantity' => round(( $event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1),
              'quantity' => round(( $event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1),
            ]);
            ApprovedPurchaseOrderDetail::createWithTransaction([
              'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
              'quantity' => round(( $event->serving_persons / $recipe->serving) * $recipeItem->item_quantity,1),
            ]);
            $purchaseOrder->events()->sync($event_id);
          }
        }
      }


      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function createChefMenu(string $id)
  {
    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      $items = Item::get();
      $result = Event::findOrFail($id);
      $servingItems = ServingItem::where('event_id', $result->id)->get();


      $results = Recipe::with('dish.dishCategory', 'chefUser')->get();
      $events = Event::whereDoesntHave('menus')->where("status", "Approved")->where('id', $id)->get();

      return view($this->view, compact('result','items', 'tiffinSizes', 'servingItems', 'places','events','results'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function storeChefMenu(StoreRequest $request)
  {
    try {
      $event_id = $request['event_id'];
      $event = Event::find($event_id);

      if (!$event) {
        redirect()->back()->with('error', 'Event not found');
      }
      $menu = Menu::createWithTransaction([
        "event_id" => $event_id,
        "item_quantity" => 'chef-input',
        "description" => $request->description
      ]);
      $menu_id = $menu->id;

      $purchaseOrder = PurchaseOrder::createWithTransaction([
        "place_id" => $request->input("place_id"),
        "status" => "approved",
        "approved_by" => Auth::id(),
        "menu_id" => $menu_id,
        'type' => 'menu'
      ]);
      foreach ($request->items as $dishId => $dishItems) {
        
        $menu->recipes()->attach($dishId);
        foreach ($dishItems as $random_index => $item) {
          // dd($itemId, $item);
          
          $this->uomBase($item, $dishId, $random_index,$menu_id,$purchaseOrder,$event_id,$dishId,$event); 
        }
      }

      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function rejectEvent(string $id)
  {
    try {
      Event::updateWithTransaction($id, ['status' => 'rejected', 'rejected_by' => Auth::id()]);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' rejected successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to reject ' . $this->controller . ': ' . $e->getMessage());
    }
  }
  public function accessPermission($name)
  {
    $user = Auth::user();
    $userRole = $user->roles()->first();
    // dd($userRole->id);
    $role_id = $userRole->id;
    $route = Route::current();
    if ($route) {
      $moduleSlug = explode('/', $route->uri())[0];
      $currentModuleId = Module::where('slug', $moduleSlug)->pluck('id')->first();
      $hasPermission = Permission::with("userPermission", 'users', 'module')->where("module_id", $currentModuleId)
      ->where("name", $name)
      ->first();
      if ($hasPermission && $hasPermission->userPermission->isNotEmpty()) {
       return $hasPermission->userPermission->contains('id', $role_id);
      }
    }
    return false;
  }
  public function monthlyEvent()
  {
    $date = now();
    $currentMonth = $date->format('M');
    $currentYear = $date->format('Y');
    $results = $this->getServingQuantityIdsWithinCurrentMonth();
    return view($this->view, compact('results', 'currentMonth', 'currentYear'));
  }
  // public function monthlyEventStore(MonthlyRequest $request)
  // {
  //   $validated = $request->validated();

  //   try {
  //     DB::beginTransaction();
  //     $validated["created_by"] = Auth::id();
  //     $hasAccess = $this->accessPermission("auto-approve");
  //     if ($hasAccess) {
  //       $validated["status"] = "Approved";
  //       $validated["approved_by"] = Auth::id();
  //     }
  //     foreach ($validated['items'] as $item) {
  //       $monthlyData = Arr::only($item, ['date', 'serving', 'name', 'description']);
  //       $otherData = Arr::only($item, ['thaal_id', 'item_id', 'tiffin_id']);
  //       if ($monthlyData["serving"] == 'tiffin') {
  //         $servingQuantity = ServingQuantity::find($otherData["tiffin_id"]);
  //         $monthlyData["start"] = "7:00:00";
  //         $monthlyData["end"] =  "10:00:00";
  //         $monthlyData["event_hours"] =  "03:00";
  //         $monthlyData["meal"] =  "lunch";
  //         $monthlyData["serving_persons"] =  $servingQuantity->serving_person;
  //       } else {
  //         $servingQuantity = ServingQuantity::find($otherData["thaal_id"]);
  //         $monthlyData["start"] = "20:30:00";
  //         $monthlyData["end"] =  "22:30:00";
  //         $monthlyData["event_hours"] =  "02:00";
  //         $monthlyData["meal"] =  "dinner";
  //         $monthlyData["no_of_thaal"] =  $servingQuantity->quantity;
  //         $monthlyData["serving_persons"] =  $servingQuantity->serving_person;
  //       }
  //       $eventId = Event::createWithTransaction($monthlyData);

  //     }
  //     $tiffinId = $otherData['tiffin_id'];
  //     $itemId = $otherData['item_id'];
  //     if (isset($tiffinId)) {
  //       $itemIdArray = explode(',', $itemId);
  //       $itemIdArray = array_map('intval', $itemIdArray);
  //       foreach ($itemIdArray as $index => $tiffanTypeId) {
  //         $servingQuantity = ServingQuantity::find($otherData["tiffin_id"]);
  //         $servingQuantityTiffin = ServingQuantityTiffin::where("serving_quantity_id", $servingQuantity->id)->where("tiffin_size_id", $tiffanTypeId)->first();
  //         ServingItem::create([
  //           'tiffin_size_id' => $tiffanTypeId,
  //           'count' => $servingQuantityTiffin->quantity,
  //           'event_id' => $eventId->id,
  //         ]);
  //       }
  //     }
  //     DB::commit();
  //     return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
  //   } catch (\Exception $e) {
  //     DB::rollBack();
  //     return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
  //   }
  // }
  public function monthlyEventStore(MonthlyRequest $request)
  {
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $validated["created_by"] = Auth::id();
      $hasAccess = $this->accessPermission("auto-approve");
      if ($hasAccess) {
        $validated["status"] = "Approved";
        $validated["approved_by"] = Auth::id();
      }

      foreach ($validated['items'] as $item) {
        $monthlyData = Arr::only($item, ['date', 'serving', 'name', 'description']);
        $otherData = Arr::only($item, ['thaal_id', 'item_id', 'tiffin_id']);
        $monthlyData["place_id"] = Auth::user()->place_id ?? '';
        if ($monthlyData["serving"] == 'tiffin') {
          $tiffinId = isset($otherData['tiffin_id']);
          $totalSum = 0;
          if ($tiffinId) {
            $itemId = $otherData['item_id'];
            $itemIdArray = explode(',', $itemId);
            $itemIdArray = array_map('intval', $itemIdArray);

            foreach ($itemIdArray as $tiffanTypeId) {
              $servingQuantity = ServingQuantity::find($otherData["tiffin_id"]);
              $servingQuantityTiffin = ServingQuantityTiffin::with("servingQuantityTiffinItems")
                ->where("serving_quantity_id", $servingQuantity->id)
                ->where("tiffin_size_id", $tiffanTypeId)
                ->first();
              if ($servingQuantityTiffin) {
                $totalSum += $servingQuantityTiffin->quantity * $servingQuantityTiffin->servingQuantityTiffinItems->person_no;
              }
            }
          }


          $servingQuantity = ServingQuantity::find($otherData["tiffin_id"]);
          $monthlyData["start"] = "07:00:00";
          $monthlyData["end"] = "10:00:00";
          $monthlyData["event_hours"] = "03:00";
          $monthlyData["meal"] = "lunch";
          $monthlyData["serving_persons"] = $totalSum;
        } else {
          $servingQuantity = ServingQuantity::find($otherData["thaal_id"]);
          $monthlyData["start"] = "20:30:00";
          $monthlyData["end"] = "22:30:00";
          $monthlyData["event_hours"] = "02:00";
          $monthlyData["meal"] = "dinner";
          $monthlyData["no_of_thaal"] = $servingQuantity->quantity;
          $monthlyData["serving_persons"] = $servingQuantity->quantity * 8;
        }

        // Attempt to create the event
        try {
          $hasAccess = $this->accessPermission("auto-approve");
          // dd($hasAccess);
          if ($hasAccess) {
            $monthlyData["status"] = "Approved";
            $monthlyData["approved_by"] = Auth::id();
          }
          $eventId = Event::createWithTransaction($monthlyData);
        } catch (\Exception $e) {
          Log::error("Event creation failed: " . $e->getMessage());
          throw ValidationException::withMessages(['items.*.date' => 'Event creation failed due to validation issues.']);
        }
        $tiffinId = isset($otherData['tiffin_id']);
        if ($tiffinId) {
          $itemId = $otherData['item_id'];
          $itemIdArray = explode(',', $itemId);
          $itemIdArray = array_map('intval', $itemIdArray);
          foreach ($itemIdArray as $tiffanTypeId) {
            $servingQuantity = ServingQuantity::find($otherData["tiffin_id"]);
            $servingQuantityTiffin = ServingQuantityTiffin::where("serving_quantity_id", $servingQuantity->id)
              ->where("tiffin_size_id", $tiffanTypeId)
              ->first();

            ServingItem::create([
              'tiffin_size_id' => $tiffanTypeId,
              'count' => $servingQuantityTiffin->quantity,
              'event_id' => $eventId->id,
            ]);
          }
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error("Failed to create " . $this->controller . ": " . $e->getMessage());
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  function getServingQuantityIdsWithinCurrentMonth()
  {
    $now = Carbon::now();
    $totalDays = $now->daysInMonth;
    $currentMonth = [];
    for ($i = 1; $i <= $totalDays; $i++) {
      $results = ServingQuantity::with("servingQuantityItems")->get();
      $currentDate = Carbon::createFromDate(null, null, $i)->toDateString();
      $dayData = [
        "date" => $i,
        "full_date" => $currentDate
      ];
      $dailyThaalEvents = Event::where('date', $currentDate)->where('serving','thaal')->get();
      $dailyTiffinEvents = Event::where('date', $currentDate)->where('serving','tiffin')->get();


      foreach ($results as $result) {
        $dateFrom = $result->date_from ? Carbon::parse($result->date_from) : null;
        $dateTo = $result->date_to ? Carbon::parse($result->date_to) : null;
        if (!$dailyThaalEvents->isEmpty()) {
          foreach ($dailyThaalEvents as $event) {
            if ($event->serving == 'thaal' && $result->serving === "Thaal") {
              if ($event->start < '20:30:00' && $event->end > '22:30:00') {
                if ($result->serving === "Thaal") {
                  $dayData["serving_thaal_id"] = $result->id;
                }
              }
            }
          }
        } else {
          if (($dateFrom && $dateTo) && $currentDate >= $dateFrom->toDateString() && $currentDate <= $dateTo->toDateString()) {
            if ($result->serving === "Thaal") {
              $dayData["serving_thaal_id"] = $result->id;
            }
          }
        }

        if (!$dailyTiffinEvents->isEmpty()) {
          foreach ($dailyTiffinEvents as $event) {
            if ($event->serving == 'tiffin' && $result->serving === "Tiffin") {
              if ($event->start < '07:00:00' && $event->end > '10:00:00') {
                if ($result->serving === "Tiffin") {
                  foreach ($result->servingQuantityItems as $item) {
                    $itemDateFrom = $item['date_from'] ? Carbon::parse($item['date_from']) : null;
                    $itemDateTo = $item['date_to'] ? Carbon::parse($item['date_to']) : null;
                    if (($itemDateFrom && $itemDateTo) && $currentDate >= $itemDateFrom->toDateString() && $currentDate <= $itemDateTo->toDateString()) {
                      $dayData["serving_tiffin_id"] = $result->id;
                      if (!isset($dayData["item_id"])) {
                        $dayData["item_id"] = [];
                      }
                      $dayData["item_id"][] = $item['tiffin_size_id'];
                    }
                  }
                }
              }
            }
          }
        } else {
          if ($result->serving === "Tiffin") {
            foreach ($result->servingQuantityItems as $item) {
              $itemDateFrom = $item['date_from'] ? Carbon::parse($item['date_from']) : null;
              $itemDateTo = $item['date_to'] ? Carbon::parse($item['date_to']) : null;
              if (($itemDateFrom && $itemDateTo) && $currentDate >= $itemDateFrom->toDateString() && $currentDate <= $itemDateTo->toDateString()) {
                $dayData["serving_tiffin_id"] = $result->id;
                if (!isset($dayData["item_id"])) {
                  $dayData["item_id"] = [];
                }
                $dayData["item_id"][] = $item['tiffin_size_id'];
              }
            }
          }
        }
      }
      if (isset($dayData["serving_thaal_id"]) || isset($dayData["serving_tiffin_id"])) {
        $currentMonth[] = $dayData;
      }
    }
    return $currentMonth;
  }

  public function uomBase($item, $recipeId, $recipeItemId,$id,$purchaseOrder,$event_id,$dishId,$event)
  {
    // dd($item,$recipeId, $recipeItemId);
    // Get input data
    $itemId = $item['ingredient_id'];
    $selectedMeasure = $item['unit_measure'];
    $itemQuantity = $item['item_quantity'];

    $itemBaseUom = ItemBaseUom::with("baseUom")->where("item_id", $itemId)->first();
    if (!$itemBaseUom) {
      return response()->json(['success' => false, 'message' => 'Base UOM not found for the given item.']);
    }
    $baseUOM = $itemBaseUom->unit_measure_id;

    $recipe = Recipe::where("id", $dishId)->first();

    if ($baseUOM == $selectedMeasure) {
      $this->createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $itemQuantity, $selectedMeasure,$id,$purchaseOrder,$event_id);

      if ($recipe) {
        MenuServing::create([
          "menu_id" => $id,
          "recipe_item_id" => null,
          "per_person_quantity" =>  ($itemQuantity != '') ? ($event->serving_persons  / $itemQuantity) : 0,
          "total_quantity" => $itemQuantity,
        ]);
      }
      return;
    }

    // Get UOM conversion
    $check = 1;
    $checking = UomConversion::where('base_uom', $baseUOM)
      ->where('secondary_uom', $selectedMeasure)
      ->first();
    if (!$checking) {
      $checking = UomConversion::where('base_uom', $selectedMeasure)
        ->where('secondary_uom', $baseUOM)
        ->first();
      $check = 2;
    }

    // Create or update the recipe item
    $convertedQuantity = $check == 1
      ? (float)(1 / $checking->conversion_value) * (float)$itemQuantity
      : (float)$checking->conversion_value * (float)$itemQuantity;


    if ($recipe) {
      MenuServing::create([
        "menu_id" => $id,
        "recipe_item_id" => null,
        "per_person_quantity" =>  ($convertedQuantity != '') ? ($event->serving_persons  / $convertedQuantity) : 0,
        "total_quantity" => $convertedQuantity,
      ]);
    }
    $this->createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $convertedQuantity, $selectedMeasure,$id,$purchaseOrder,$event_id);
  }

  private function createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $itemQuantity, $selectedMeasure,$id,$purchaseOrder,$event_id)
  {
    $data = [
      'recipe_id' => $recipeId,
      'recipe_item_id' => null,
      'menu_id' => $id,
      'item_id' => $item['ingredient_id'],
      'select_item_quantity' => $item['item_quantity'],
      'item_quantity' => $itemQuantity,
      'select_measurement_id' => $selectedMeasure,
      'measurement_id' => $baseUOM,
      'description' => $item['description'],
      'created_by' => Auth::id(),
    ];
    
    $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
      'purchase_order_id' => $purchaseOrder->id,
      'event_id' => $event_id,
      'recipe_id' => $recipeId,
      'item_id' => $item['ingredient_id'],
      'unit_measure_id' => $baseUOM,
      'select_unit_measure_id' => $selectedMeasure,
      'select_quantity' => $item['item_quantity'],
      'quantity' => $itemQuantity,
    ]);
    ApprovedPurchaseOrderDetail::createWithTransaction([
      'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
      'quantity' => $itemQuantity,
    ]);
    $purchaseOrder->events()->sync($event_id);

    ChefRecipeItem::create(
      $data
    );
  }
}
