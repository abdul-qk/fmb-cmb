<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\AddRequest;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\ChefRecipeItem;
use App\Models\Dish;
use App\Models\Event;
use App\Models\Menu;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\MenuDish;
use App\Models\MenuServing;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Inventory;
use App\Models\ItemBaseUom;
use App\Models\ServingItem;
use App\Models\TiffinSize;
use App\Models\UomConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class MenuController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = Menu::with('recipes.chefUser', 'recipes.dish', 'event.place', 'purchaseOrder', 'createdBy', 'updatedBy')->get();
    // dd($results->toArray());
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $results = Recipe::with('dish', 'chefUser')->get();
    $events = Event::whereDoesntHave('menus')->where("status", "Approved")->get();
    // dd($results);
    return view($this->view, compact("results", "events"));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {
    try {
      $validated = $request->validated();
      $menuData = Arr::except($validated, ['dish_id']);
      $dishes = Arr::get($validated, 'dish_id', []);
      $event_id = $menuData['event_id'];
      $event = Event::find($event_id);

      if (!$event) {
        redirect()->back()->with('error', 'Event not found');
      }
      $menu = Menu::createWithTransaction($menuData);
      foreach ($dishes as $dish_id) {
        $menu->recipes()->attach($dish_id);
        $recipe = Recipe::where("id", $dish_id)->first();
        if ($recipe) {
          $recipeItems = RecipeItem::where("recipe_id", $recipe->id)->get();
          foreach ($recipeItems as $recipeItem) {
            MenuServing::create([
              "menu_id" => $menu->id,
              "recipe_item_id" => $recipeItem->id,
              "per_person_quantity" => $request->input('item_quantity') == 'recipe' ? (($recipeItem->item_quantity > 0) ? ($recipe->serving / $event->serving_persons) : 0) : 0,
              "total_quantity" => $request->input('item_quantity') == 'recipe' ?  ($recipeItem->item_quantity * ($recipe->serving / $event->serving_persons)) : 0,
            ]);
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

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      // Attempt to retrieve the event with the specified menu ID
      // $result = Event::with([
      //   'menus.dishes.recipes.recipeItems.measurement',
      //   'menus.dishes.recipes.recipeItems.item',
      //   'menus.event.place',
      //   'menus.dishes.dishCategory'
      // ])
      //   ->whereHas('menus', function ($query) use ($id) {
      //     $query->where('id', $id);
      //   })
      //   ->first()
      //   ?->menus
      //   ->firstWhere('id', $id);


      // dd($result->toArray());


      $result = Menu::with([
        'recipes.chefUser',
        'recipes.recipeItems.measurement',
        'recipes.recipeItems.item' => function ($query) use ($id) {
          $query->with('purchaseOrderDetails', function ($query) use ($id) {
            $query->whereHas('purchaseOrder', function ($query) use ($id) {
              $query->where('menu_id', $id);
            });
          });
        },
        'recipes.dish.dishCategory',
        'event.place',
      ])
        ->findOrFail($id);

      $recipes = Recipe::with(['dish.dishCategory', 'chefRecipeItems.recipeItem', 'chefRecipeItems.measurement', 'chefRecipeItems.item', 'chefRecipeItems' => function ($query) use ($id) {
        $query->where('menu_id', $id);
      }])->get();

      if (!$result) {
        return redirect()->back()->with('error', 'Menu not found');
      }
      $tiffinSizes = TiffinSize::get();
      $servingItems = ServingItem::where('event_id', $result->event_id)->get();

      // dd($result->toArray());
      return view($this->view, compact('result', 'recipes','tiffinSizes','servingItems'));
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
      // $results = Recipe::with('dish')->get();
      // $events = Event::whereDoesntHave('menus')->get();
      // $menu = Menu::findOrFail($id);
      // $selectedItems = $menu->dishes->pluck('id')->toArray();
      // dd($selectedItems);

      $results = Recipe::with('dish')->get();

      $menu = Menu::findOrFail($id);
      $selectedEvent = $menu->event;
      $eventsWithoutMenus = Event::whereDoesntHave('menus')->where("status", "Approved")->get();
      if ($selectedEvent) {
        $events = $eventsWithoutMenus->prepend($selectedEvent);
      } else {
        $events = $eventsWithoutMenus;
      }
      $selectedItems = $menu->recipes->pluck('id')->toArray();
      return view($this->view, compact('results', 'menu', 'events', 'selectedItems'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, string $id)
  {
    $validated = $request->validated();
    DB::beginTransaction();
    try {
      $validated = $request->validated();
      $menuData = Arr::except($validated, ['dish_id']);
      $dishes = Arr::get($validated, 'dish_id', []);
      $event_id = $menuData['event_id'];
      $event = Event::find($event_id);

      if (!$event) {
        redirect()->back()->with('error', 'Event not found');
      }

      $menu = Menu::updateWithTransaction($id, $menuData);
      $menu->recipes()->sync($dishes);
      $menu->menuServings()->forceDelete();
      foreach ($dishes as $dish_id) {
        $recipe = Recipe::where("id", $dish_id)->first();
        if ($recipe) {
          $recipeItems = RecipeItem::where("recipe_id", $recipe->id)->get();
          foreach ($recipeItems as $recipeItem) {
            MenuServing::create([
              "menu_id" => $menu->id,
              "recipe_item_id" => $recipeItem->id,
              "per_person_quantity" => $request->input('item_quantity') == 'recipe' ? (($recipeItem->item_quantity > 0) ? ($recipe->serving / $event->serving_persons) : 0) : 0,
              "total_quantity" => $request->input('item_quantity') == 'recipe' ?  ($recipeItem->item_quantity * ($recipe->serving / $event->serving_persons)) : 0,

              // "per_person_quantity" => ($recipeItem->item_quantity > 0) ? ($event->serving_persons / $recipe->serving) : 0,
              // "total_quantity" => (($event->serving_persons / $recipe->serving) * $recipeItem->item_quantity),
            ]);
          }
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      // $menu = Menu::findOrFail($id);
      // $menu->menuServings()->forceDelete();
      // // $menu->delete();
      Menu::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function approve(string $id)
  {
    try {
      $menu_id = $id;
      $result = Menu::with('recipes.chefUser', 'recipes.recipeItems.measurement', 'recipes.recipeItems.item', 'recipes.dish.dishCategory', 'event.place')->findOrFail($menu_id);
      if (!$result) {
        return redirect()->back()->with('error', 'Menu not found');
      }
      return view($this->view, compact('result', 'menu_id'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }
  public function storeApprove(Request $request)
  {
   
    DB::beginTransaction();
    try {
      $purchaseOrderItemsData = $request->input("items");
      $place_id = $request->input("place_id");
      $event_id = $request->input("event_id");
      $menu_id = $request->input("menu_id");

      $purchaseOrder = PurchaseOrder::createWithTransaction([
        "place_id" => $place_id,
        "status" => "approved",
        "approved_by" => Auth::id(),
        "menu_id" => $menu_id,
        'type' => 'menu'
      ]);

      foreach ($purchaseOrderItemsData as $key => $purchaseOrderItemData) {
        $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
          'purchase_order_id' => $purchaseOrder->id,
          'event_id' => $event_id,
          'recipe_id' => $purchaseOrderItemData['recipe_id'],
          'item_id' => $purchaseOrderItemData['item_id'],
          'unit_measure_id' => $purchaseOrderItemData['unit_id'],
          'select_unit_measure_id' => $purchaseOrderItemData['unit_id'],
          'select_quantity' => $purchaseOrderItemData['current_quantity'],
          'quantity' => $purchaseOrderItemData['quantity'],
        ]);
        ApprovedPurchaseOrderDetail::createWithTransaction([
          'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
          'quantity' => $purchaseOrderItemData['quantity'],
        ]);
      }
      $purchaseOrder->events()->sync($event_id);
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function chefInput(string $id)
  {
    try {
     
      
      $items = Item::get();
      $result = Menu::with([
        'recipes.chefUser',
        'recipes.recipeItems.measurement',
        'recipes.recipeItems.item' => function ($query) use ($id) {
          $query->with('purchaseOrderDetails', function ($query) use ($id) {
            $query->whereHas('purchaseOrder', function ($query) use ($id) {
              $query->where('menu_id', $id);
            });
          });
        },
        'recipes.dish.dishCategory',
        'event.place',
      ])
      
        ->findOrFail($id);

        $tiffinSizes = TiffinSize::get();
        $servingItems = ServingItem::where('event_id', $result->event_id)->get();
      // dd($servingItems->toArray());
      // If the menu is not found, handle the error appropriately
      if (!$result) {
        return redirect()->back()->with('error', 'Menu not found');
      }

      return view($this->view, compact('result', 'items','tiffinSizes','servingItems'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function storeChefInput(Request $request, string $id)
  {
    DB::beginTransaction();
    try {
    
      $event_id = $request->input("event_id");

      $menu = Menu::where("event_id", $event_id)->first();

      if ($menu) {
        Menu::updateWithTransaction($menu->id, [
          'description' => $request->input('description'),
        ]);
      }

      $purchaseOrder = PurchaseOrder::createWithTransaction([
        "place_id" => $request->input("place_id"),
        "status" => "approved",
        "approved_by" => Auth::id(),
        "menu_id" => $id,
        'type' => 'menu'
      ]);

      foreach ($request->items as $recipeId => $recipeItems) {


        foreach ($recipeItems as $recipeItemId => $item) {
          // dd($itemId, $item);
          $this->uomBase($item, $recipeId, $recipeItemId, $id, $purchaseOrder, $event_id); // Pass recipeId and itemId to the function
        }
      }

      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function editChefInput(string $id)
  {
    // dd("s");
    try {
      $items = Item::get();
      $result = Menu::with([
        'recipes.chefUser',
        'recipes.recipeItems.measurement',
        'recipes.recipeItems.item.purchaseOrderDetail',
        'recipes.recipeItems.item' => function ($query) use ($id) {
          $query->with('purchaseOrderDetails', function ($query) use ($id) {
            $query->whereHas('purchaseOrder', function ($query) use ($id) {
              $query->where('menu_id', $id);
            });
          });
        },
        'recipes.dish.dishCategory',
        'event.place',
      ])
        ->findOrFail($id);
        $purchaseOrderDetail = PurchaseOrderDetail::where("event_id",$result->event_id)->first();
        
        $purchaseOrderIds =  $purchaseOrderDetail->purchase_order_id ?? "";

      $recipes = Recipe::with([
        'dish.dishCategory',
        'chefRecipeItems' => function ($query) use ($id) {
          $query->where('menu_id', $id)
            ->with(['recipeItem', 'measurement', 'item']);
        }
      ])
        ->whereHas('chefRecipeItems', function ($query) use ($id) {
          $query->where('menu_id', $id);
        })
        ->get();

        $dishIds = Recipe::whereHas('chefRecipeItems', function ($query) use ($id) {
          $query->where('menu_id', $id);
      })
      ->pluck('dish_id');

      $dishRecipes = Recipe::with('dish.dishCategory', 'chefUser')->get();


      $tiffinSizes = TiffinSize::get();
      $servingItems = ServingItem::where('event_id', $result->event_id)->get();
      // dd($recipes->toArray());
      if (!$result) {
        return redirect()->back()->with('error', 'Menu not found');
      }
      // dd($recipes->toArray());
      return view($this->view, compact('result', 'items', 'recipes', 'dishRecipes','dishIds','purchaseOrderIds','tiffinSizes','servingItems'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function updateChefInput(Request $request, string $id)
  {
    // dd($request->all());
    DB::beginTransaction();
    try {
      $dishes = $request->input('dish_id');
      $event_id = $request->input("event_id");


      $event = Event::find($event_id); 
      $menu = Menu::updateWithTransaction($id, [
        'event_id' => $event_id,
        'description' => $request->input('description'),
      ]);

      $menu->recipes()->sync($dishes);
      $menu->menuServings()->forceDelete();

      ChefRecipeItem::where('menu_id', $id)->delete();
      $oldPurchaseOrder = PurchaseOrder::find($request->input("purchase_order_id"));
      if ($oldPurchaseOrder) {
        $oldPurchaseOrder->detail()->each(function ($detail) {
          ApprovedPurchaseOrderDetail::where("purchase_order_detail_id", $detail->id)->delete();
          $detail->delete();
        });
        $oldPurchaseOrder->events()->detach();
        $oldPurchaseOrder->delete();
      }


      $purchaseOrder = PurchaseOrder::createWithTransaction([
        "place_id" => $request->input("place_id"),
        "status" => "approved",
        "approved_by" => Auth::id(),
        "menu_id" => $id,
        'type' => 'menu'
      ]);

      foreach ($request->items as $recipeId => $recipeItems) {
        foreach ($recipeItems as $recipeItemId => $item) {
          // dd($itemId, $item);
          $this->uomBase($item, $recipeId, $recipeItemId, $id, $purchaseOrder, $event_id, $recipeId,$event); // Pass recipeId and itemId to the function
        }
      }

      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function uomBase($item, $recipeId, $recipeItemId, $id, $purchaseOrder, $event_id,$dishId = null,$event = null)
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

    if ($baseUOM == $selectedMeasure) {
      $this->createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $itemQuantity, $selectedMeasure, $id, $purchaseOrder, $event_id);
      
      if($dishId == ! null) {
        $recipe = Recipe::where("id", $dishId)->first();
  
        if ($recipe) {
          MenuServing::create([
            "menu_id" => $id,
            "recipe_item_id" => null,
            "per_person_quantity" =>  ($itemQuantity != '') ? ($event->serving_persons  / $itemQuantity) : 0,
            "total_quantity" => $itemQuantity,
          ]);
        }

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

    if($dishId == ! null) {
      $recipe = Recipe::where("id", $dishId)->first();
      if ($recipe) {
        MenuServing::create([
          "menu_id" => $id,
          "recipe_item_id" => null,
          "per_person_quantity" =>  ($convertedQuantity != '') ? ($event->serving_persons  / $convertedQuantity) : 0,
          "total_quantity" => $convertedQuantity,
        ]);
      }
    }
    $this->createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $convertedQuantity, $selectedMeasure, $id, $purchaseOrder, $event_id);
  }

  private function createOrUpdateRecipeItem($item, $recipeId, $recipeItemId, $baseUOM, $itemQuantity, $selectedMeasure, $id, $purchaseOrder, $event_id)
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
