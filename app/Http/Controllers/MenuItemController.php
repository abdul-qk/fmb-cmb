<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\AddRequest;
use App\Models\Dish;
use App\Models\Event;
use App\Models\Menu;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\MenuDish;
use App\Models\MenuServing;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ServingItem;
use App\Models\TiffinSize;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuItemController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // $results = Menu::with()->get();
    // $results = Event::with('menus.dishes.recipes.recipeItems')->select('id', 'name')->get();
    $menus = Menu::with(['createdBy', 'updatedBy', 'event.place', 'chefMenuItems.item', 'chefMenuItems.measurement', 'chefMenuItems.recipe.dish', 'chefMenuItems.recipe.chefUser', 'chefMenuItems', 'recipes.dish', 'recipes.recipeItems.measurement', 'recipes.recipeItems.item.purchaseOrderDetail', 'recipes.chefUser'])->get();
    // dd($menus->toArray());

    $results = collect();
    foreach ($menus as $menu) {
      // dd($menu->createdBy->name);
      // Initialize the dish data for the current menu
      $dishData = [];

      // Event data for the current menu
      $eventData = [
        'id' => $menu->id, // Place name
        'place' => $menu->event->place->name, // Place name
        'date' => $menu->event->created_at->format('Y-m-d'),
        'event' => $menu->event->name,
        'start' => $menu->event->start,
        'end' => $menu->event->end,
        'item_quantity' => $menu->item_quantity,
        'created' => $menu->created_at->format('Y-m-d H:i:s'),
        'created_by' => $menu->createdBy->name,
        'last_modified' => $menu->updated_at ? $menu->updated_at->format('Y-m-d H:i:s') : '',
        'last_modified_by' => $menu->updatedBy ? $menu->updatedBy->name : '-',
      ];

      if ($menu->item_quantity == "recipe") {

        foreach ($menu->recipes as $recipe) {
          // Initialize recipe items for the current dish
          $recipeItemData = [];
          // dd($recipe);
          // Extract and add recipe items for each dish
          foreach ($recipe->recipeItems as $recipeItem) {
            // dd(
            // $recipeItem->item->toArray()
            // );

            $recipeItemData[] = [
              'Item' => $recipeItem->item->name ?? 'N/A', // fallback if name doesn't exist
              'Quantity' => optional($recipeItem->item->purchaseOrderDetails->where('recipe_id', $recipeItem->recipe_id)->first())->quantity
                ?? round(($menu->event->serving_persons / $recipe->serving) * $recipeItem->item_quantity, 1),
              'UOM' => $recipeItem->measurement->short_form ?? 'N/A', // fallback for missing measurement
            ];
          }

          // Add dish data along with recipe items
          $dishData[] = [
            'dish' => $recipe->dish->name,
            'chef' => $recipe->chefUser->name,
            'recipe_items' => $recipeItemData,
          ];
        }
        $mergedData = array_merge($eventData, ['dishes' => $dishData]);
        $results->push($mergedData);
      } else {
        $dishData = [];

        foreach ($menu->chefMenuItems->reverse() as $chefMenuItem) {
          $recipeKey = $chefMenuItem->recipe->id;

          if (!isset($dishData[$recipeKey])) {
            $dishData[$recipeKey] = [
              'dish' => $chefMenuItem->recipe->dish->name ?? 'N/A',
              'chef' => $chefMenuItem->recipe->chefUser->name ?? 'N/A',
              'recipe_items' => [],
            ];
          }

          $dishData[$recipeKey]['recipe_items'][] = [
            'Item' => $chefMenuItem->item->name ?? 'N/A',
            'Quantity' => $chefMenuItem->item_quantity,
            'UOM' => $chefMenuItem->measurement->short_form ?? 'N/A',
          ];
        }

        // Optionally reindex to make it a clean array (not keyed by recipe ID)
        $dishData = array_values($dishData);

        $mergedData = array_merge($eventData, ['dishes' => $dishData]);
        $results->push($mergedData);
      }
    }
    // dd($results->toArray());
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create() {}

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request) {}

  /**
   * Display the specified resource.
   */
  // public function show(string $id)
  // {
  //   try {

  //     $results = Event::with('menus.dishes.recipes.recipeItems.measurement','menus.dishes.recipes.recipeItems.item','menus.event.place','menus.dishes.dishCategory')
  //       ->whereHas('menus', function ($query) use ($id) {
  //         $query->where('id', $id);
  //       })->first();
  //     // return $results;
  //     return view($this->view, compact('results'));
  //   } catch (\Exception $e) {
  //     return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
  //   }
  // }
  public function show(string $id)
  {
    try {
      $result =  Menu::with(['createdBy', 'updatedBy', 'event.place', 'recipes.dish', 'recipes.recipeItems.measurement', 'recipes.recipeItems.item.purchaseOrderDetail', 'recipes.chefUser'])->find($id);
      // dd( $result->toArray());
      // Attempt to retrieve the event with the specified menu ID



      // If the menu is not found, handle the error appropriately
      if (!$result) {
        return redirect()->back()->with('error', 'Menu not found');
      }

      $recipes = Recipe::with(['dish.dishCategory', 'chefRecipeItems.recipeItem', 'chefRecipeItems.measurement', 'chefRecipeItems.item', 'chefRecipeItems' => function ($query) use ($id) {
        $query->where('menu_id', $id);
      }])->get();

      $tiffinSizes = TiffinSize::get();
      $servingItems = ServingItem::where('event_id', $result->event_id)->get();

      // dd($result->toArray());
      return view($this->view, compact('result', 'recipes','servingItems','tiffinSizes'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id) {}

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, string $id) {}

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {}
}
