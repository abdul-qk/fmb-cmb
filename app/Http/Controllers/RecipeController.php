<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\AddRequest;
use App\Models\Dish;
use App\Models\Item;
use App\Models\ItemBaseUom;
use App\Models\Place;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\UnitMeasure;
use App\Models\UomConversion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class RecipeController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = Recipe::with(['dish.dishCategory', 'recipeItem', 'place', 'place.location', "chefUser", 'createdBy', 'updatedBy'])->get();
    // dd( $results->toArray());
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $dishes = Dish::get();
    $items = Item::get();
    $places = Place::with(['country', 'city'])->get();
    $unitMeasures = UnitMeasure::get();

    $chefs = User::whereHas('roles', function ($query) {
      $query->where('name', ['Chef']);
    })->select('users.*')->with('roles')->get();

    return view($this->view, compact('dishes', 'items', 'unitMeasures', 'places', 'chefs'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {

    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $recipe = Recipe::createWithTransaction($validated);
      $totalIngredient = $request->total_ingredient;
      $recipe = $recipe->id;
      for ($i = 1; $i <= $totalIngredient; $i++) {
        $this->uomBase($recipe, $request, $i);
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('message', 'Recipe created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create Recipe: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      $dishes = Dish::get();
      $items = Item::get();
      $unitMeasures = UnitMeasure::get();
      $result = Recipe::findOrFail($id);
      $recipeItems =  RecipeItem::where("recipe_id", $id)->get();
      $places = Place::with(['country', 'city'])->get();
      $totalRecipeItems = $recipeItems->count();

      $chefs = User::whereHas('roles', function ($query) {
        $query->where('name', ['Chef']);
      })->select('users.*')->with('roles')->get();
      // dd($recipeItems->count());
      return view($this->view, compact('result', 'dishes', 'items', 'unitMeasures', 'totalRecipeItems', 'recipeItems', 'places', 'chefs'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find Item: ' . $e->getMessage());
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    try {
      $dishes = Dish::get();
      $items = Item::get();
      $unitMeasures = UnitMeasure::get();
      $result = Recipe::findOrFail($id);
      $recipeItems =  RecipeItem::where("recipe_id", $id)->get();
      $totalRecipeItems = $recipeItems->count();
      $places = Place::with(['country', 'city'])->get();
      $chefs = User::whereHas('roles', function ($query) {
        $query->where('name', ['Chef']);
      })->select('users.*')->with('roles')->get();
      // dd($recipeItems->count());
      return view($this->view, compact('result', 'dishes', 'items', 'unitMeasures', 'totalRecipeItems', 'recipeItems', 'places', 'chefs'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find Item: ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, string $id)
  {
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $recipe = Recipe::updateWithTransaction($id, $validated);
      $totalIngredient = $request->input('total_ingredient');
      $recipe = $recipe->id;
      for ($i = 1; $i <= $totalIngredient; $i++) {
        $this->uomBase($recipe, $request, $i);
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('message', 'Recipe created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create Recipe: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      DB::transaction(function () use ($id) {
        // Find the recipe by ID or fail if not found
        $recipe = Recipe::findOrFail($id);

        // Delete associated RecipeItem records
        $recipe->recipeItems()->delete();

        // Delete the recipe
        $recipe->delete();
      });
      return redirect()->route($this->redirect)->with('success', 'Recipe and its items deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete Recipe: ' . $e->getMessage());
    }
  }

  public function uomBase($recipe, $request, $i)
  {
    // Get input data
    $itemId = $request->input('item_' . $i);
    $selectedMeasure = $request->input('unit_measure_' . $i);
    $itemQuantity = $request->input('item_quantity_' . $i);

    $itemBaseUom = ItemBaseUom::with("baseUom")->where("item_id", $itemId)->first();
    if (!$itemBaseUom) {
      return response()->json(['success' => false, 'message' => 'Base UOM not found for the given item.']);
    }
    $baseUOM = $itemBaseUom->unit_measure_id;

    if ($baseUOM == $selectedMeasure) {
      $this->createOrUpdateRecipeItem($recipe, $request, $i, $baseUOM, $itemQuantity, $selectedMeasure);
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

    $this->createOrUpdateRecipeItem($recipe, $request, $i, $baseUOM, $convertedQuantity, $selectedMeasure);
  }

  private function createOrUpdateRecipeItem($recipe, $request, $i, $baseUOM, $itemQuantity, $selectedMeasure)
  {
    $data = [
      'recipe_id' => $recipe,
      'item_id' => $request->input('item_' . $i),
      'select_item_quantity' => $request->input('item_quantity_' . $i),
      'select_measurement_id' => $selectedMeasure,
      'description' => $request->input('description_' . $i),
      'measurement_id' => $baseUOM,
      'item_quantity' => $itemQuantity,
    ];

    // Use updateOrCreate to handle both create and update scenarios
    RecipeItem::updateOrCreate(
      ['id' => $request->input('recipeItem_' . $i)], // Find by the RecipeItem ID
      $data
    );
  }
}
