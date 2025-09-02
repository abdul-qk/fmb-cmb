<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\City;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Item;
use App\Models\UnitMeasure;
use App\Models\VendorSupplyCategory;
use App\Models\Module;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Event;
use App\Models\ItemBaseUom;
use App\Models\UomConversion;
use App\Models\InventoryDetail;
use App\Models\Menu;
use App\Models\Place;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Recipe;
use App\Models\ServingItem;
use App\Models\ServingQuantity;
use App\Models\ServingQuantityTiffin;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Scopes\OrderByIdDescScope;

class FetchController extends Controller
{
  public function __construct()
  {
    // Added to ignore BaseController`s constructor
  }

  public function fetchDishCategory(Request $request)
  {

    $dish = Dish::where('id', $request->id)->first();
    // dd($dish->category_id);
    $dishCategory = DishCategory::where('id', $dish->category_id)->get();
    return [
      'result' => $dishCategory
    ];
  }
  public function fetchUnit(Request $request)
  {
    $purchaseOrderDetail = collect();
    $results = ItemBaseUom::with(['item', 'unitMeasure', 'baseUom'])
    ->where('item_id', $request->id)
    ->get()
    ->flatMap(function ($itemBaseUom) use ($request) {
        // Merge `base_uom` and `unit_measure` data
        $baseUomCollection = $itemBaseUom->baseUom ? collect([$itemBaseUom->baseUom]) : collect();
        $mergedUnitMeasures = $baseUomCollection->merge($itemBaseUom->unitMeasure);

        // Choose collection based on $request->baseID
        $unitMeasures = $request->baseHas ? $baseUomCollection : $mergedUnitMeasures;

        return $unitMeasures->map(function ($unitMeasure) {
            return [
                'id' => $unitMeasure->id,
                'name' => $unitMeasure->name,
                'short_form' => $unitMeasure->short_form,
            ];
        });
    });
    if($request->vendor_id) {
      $purchaseOrderIds = PurchaseOrder::where("vendor_id", $request->vendor_id)->pluck('id')->toArray();
      $purchaseOrderDetail ="";
      // Check if there are any matching purchase order IDs
      if (!empty($purchaseOrderIds)) {
        $purchaseOrderDetail = PurchaseOrderDetail::whereIn("purchase_order_id", $purchaseOrderIds)
          ->where("item_id", $request->id)
          ->first();
      } else {
        $purchaseOrderDetail = collect(); // Return an empty collection if no matching purchase orders exist
      }
      
    }

    return [
      'result' => $results,
      'purchaseOrderDetail' => $purchaseOrderDetail
    ];
  }

  public function getIngredient(Request $request)
  {
    $id = $request->id;
    $currentItems = $request->items ?? '';

    $unitMeasures = UnitMeasure::get();
    $items = Item::get();

    $currentIngredient =  '<div class="col-12 ingredient-detail-' . $id . '">  
    <div class="row">
    
    <div class="col-md-3 mb-5">
                  <label class="form-label required">Item</label>
                  <select required class="form-select item" name="item_' . $id . '" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    foreach ($items as $item) {
      if(!empty($currentItems)) {
        if (!in_array($item->id, $currentItems)) {
          $currentIngredient .= '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->name) . '</option>';
        }
      }else {
        $currentIngredient .= '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->name) . '</option>';
      }
    }
    $currentIngredient .=  '</select>
                </div>
                <div class="col-md-3 mb-5">
                  <label class="form-label required">Item Quantity</label>
                  <input required type="number" step="0.001" class="form-control" name="item_quantity_' . $id . '" id="item_quantity">
                </div>
                <div class="col-md-3 mb-5">
                  <label class="form-label required">Unit of Measure</label>
                  <div class="position-relative">
                    <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                      <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  <select required class="form-select unit_measure" name="unit_measure_' . $id . '" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    // foreach ($unitMeasures as $unitMeasure) {
    //   $currentIngredient .=  '<option value="' . $unitMeasure->id . '" ' . '>' . $unitMeasure->name . '</option>';
    // }
    $currentIngredient .=  ' </select></div>
                </div>
                <div class="col-md-3 mb-5 description-box">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label">Description</label>
                    <i id="ingredient-detail-' . $id . '" class="fa-regular fa-trash-can text-danger cursor-pointer remove-btn" style="cursor: pointer;"></i>
                  </div>
                  <textarea class="form-control" name="description_' . $id . '"></textarea>
                  
                </div>
                
                </div>
    </div>';

    return [
      'currentIngredient' => $currentIngredient
    ];
  }

  public function getIngredientChef(Request $request)
  {
    $id = $request->id;
    $currentItems = $request->items;
    $recipe_id = $request->recipe_id;

    $unitMeasures = UnitMeasure::get();
    $items = Item::get();
    $randomNumber = random_int(1000, 9999);
    $currentIngredient =  '  
    <tr>
    
    <td >
                  <select required class="form-select item" name="items['.$recipe_id.']['.$randomNumber.'][ingredient_id]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    foreach ($items as $item) {
      if (!in_array($item->id, $currentItems)) {
        $currentIngredient .= '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->name) . '</option>';
      }
    }
    $currentIngredient .=  '</select>
                </td>
                <td>
                  <input required type="number" step="0.001" class="form-control" name="items['.$recipe_id.']['.$randomNumber.'][item_quantity]" id="item_quantity">
                </td>
                <td>
                  <div class="position-relative">
                    <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                      <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  <select required class="form-select unit_measure" name="items['.$recipe_id.']['.$randomNumber.'][unit_measure]" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    // foreach ($unitMeasures as $unitMeasure) {
    //   $currentIngredient .=  '<option value="' . $unitMeasure->id . '" ' . '>' . $unitMeasure->name . '</option>';
    // }
    $currentIngredient .=  ' </select></div>
                </td>
                <td class="text-center description-box" style="vertical-align:middle">
                <svg data-dish-id="'.$recipe_id.'" class="add-more-button me-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                    <g>
                      <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                      <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                      <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                    </g>
                  </svg>
                  <svg id="ingredient-detail-' . $id . '" title="Remove" class="remove-btn cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 384 384">
                    <g>
                      <path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" />
                    </g>
                  </svg>
                  
                <textarea class="form-control d-none" name="items['.$recipe_id.']['.$randomNumber.'][description]"></textarea>
              </td>
                
              </tr>';

    return [
      'currentIngredient' => $currentIngredient
    ];
  }
  public function getItem(Request $request)
  {
    $id = $request->id;
    $currentItems = $request->items;
    $innerItem = $request->innerItem;

   
    $items = Item::get();

    $currentIngredient =  '  
    <tr class="">
    <td>
    <div class="d-none">
      <input type="hidden" class="remaining_quantity" name="" value="1000000">
      <input type="checkbox" name="selected_items[]" value="' . $id . '" class=" form-check-input">
    </div>
                  <select  class="form-select item" name="item_' . $id . '" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    foreach ($items as $item) {
      if (isset($currentItems)) {
        if (!in_array($item->id, $currentItems)) {
          $currentIngredient .= '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->name) . '</option>';
        }
        
      }else {
        $currentIngredient .= '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->name) . '</option>';
      }
    }
    $currentIngredient .=  '</select>
                </td>
                 <td>
                    <input type="text" class="form-control base-uom w-150px w-lg-100" readonly>
                  </td>
                  <td>
                    <input type="number" step="0.001" class="form-control available-quantity w-150px w-lg-100" readonly>
                  </td>
               
                <td>
                 
                  <div class="position-relative">
                    <div class="unit_id_loader align-items-center bg-gray-100 d-none h-100 justify-content-center position-absolute start-50 top-50 translate-middle w-100 z-index-2">
                      <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  <select class="form-select unit_measure" name=""  data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    // foreach ($unitMeasures as $unitMeasure) {
    //   $currentIngredient .=  '<option value="' . $unitMeasure->id . '" ' . '>' . $unitMeasure->name . '</option>';
    // }
    $currentIngredient .=  ' </select></div>
                </td>
                 <td>
                 <input type="number" step="0.001" class="form-control item_quantity" name="" id="item_quantity">
                 </td>
                 <td style="text-align: center;vertical-align: middle;cursor: pointer;">
                 <input hidden type="text" class="other" value="0" name="other"/> 
                  <svg class="add-items" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="cursor-pointer">
                            <g>
                              <path d="M18 2c2.206 0 4 1.794 4 4v12c0 2.206-1.794 4-4 4H6c-2.206 0-4-1.794-4-4V6c0-2.206 1.794-4 4-4zm0-2H6a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h12a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                              <path d="M12 18a1 1 0 0 1-1-1V7a1 1 0 0 1 2 0v10a1 1 0 0 1-1 1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                              <path d="M6 12a1 1 0 0 1 1-1h10a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1z" fill="var(--bs-primary)" opacity="1" data-original="#000000" class=""></path>
                            </g>
                          </svg>';

                          if ($innerItem >= 2) {
                            $currentIngredient .=    '
                                 <svg title="Remove" class="remove-btn ms-3" data-row-id="item-row-1" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 384 384" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M64 341.333C64 364.907 83.093 384 106.667 384h170.667C300.907 384 320 364.907 320 341.333v-256H64v256zM266.667 21.333 245.333 0H138.667l-21.334 21.333H42.667V64h298.666V21.333z" fill="#f42c2c" opacity="1" data-original="#000000" class=""></path></g></svg>
                                  </td>';
                           }   

             $currentIngredient .= '</tr>';

    return [
      'currentIngredient' => $currentIngredient
    ];
  }

  public function cities(Request $request)
  {
    $port = City::where('country_id', $request->id)->orderBy('name', 'asc')->get();
    return [
      'cities' => $port
    ];
  }
  public function getSupplyCategory(Request $request)
  {
    $id = $request->id;

    $supplyCategories = VendorSupplyCategory::get();

    $currentSupply =  '<div class="col-md-6 mb-5 supply-row">  
                    <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label required">Supply ' . $id . '</label>
                    <i class="fa-regular fa-trash-can text-danger cursor-pointer remove-supply" style="cursor: pointer;"></i>
                  </div>
                  <select required class="form-select " name="supplyCategory_id_' . $id . '" data-control="select2" data-close-on-select="false" data-placeholder="Select" data-allow-clear="true">
                    <option value=""></option>';
    foreach ($supplyCategories as $item) {
      $currentSupply .=  '<option value="' . $item->id . '" ' . '>' . $item->name . '</option>';
    }
    $currentSupply .=  '</select>
                </div>';

    return [
      'currentSupply' => $currentSupply
    ];
  }

  public function fetchModuleDisplayOrder(Request $request)
  {
    $parentId = $request->parent_id;
    $currentModuleId = $request->current_module_id;
    $displayOrder = Module::where('parent_id', $parentId)->where('id', '!=', $currentModuleId)->orderByDesc('display_order')->pluck('display_order')->first() + 1;
    return $displayOrder;
  }
  public function miscDocuments($fileName)
  {
    $name = $this->name('app/public/uploads/users/', 'misc_documents', $fileName);
    $folderPath = storage_path($name);
    if (!file_exists($folderPath)) {
      return abort(404, 'File not found: ' . $folderPath);
    }

    return response()->file($folderPath);
  }
  public function medicalDocuments($fileName)
  {
    $name = $this->name('app/public/uploads/users/', 'medical_documents', $fileName);
    $folderPath = storage_path($name);
    if (!file_exists($folderPath)) {
      return abort(404, 'File not found: ' . $folderPath);
    }
    return response()->file($folderPath);
  }
  public function nationalIdentityDocuments($fileName)
  {
    $name = $this->name('app/public/uploads/users/', 'upload_national_identity', $fileName);
    $folderPath = storage_path($name);
    if (!file_exists($folderPath)) {
      return abort(404, 'File not found: ' . $folderPath);
    }
    return response()->file($folderPath);
  }
  protected function name($path, $folder, $file)
  {

    $parts = explode('=', $file);

    $user = User::findOrFail($parts[0]);
    $user_name = ucwords(Str::of($user->name)->lower());
    $user_name = str_replace(' ', '', $user_name);
    $formatted_user_name = 'FMB_' . $user->id . '_' . $user_name;
    return $path . '' . $formatted_user_name . '/' . $folder . '/' . $parts[1];
  }
  public function fetchVendorItems(Request $request)
  {
    $vendorId = $request->vendor_id;
    $units = UnitMeasure::all();
    $vendorItems = Vendor::withWhereHas('items')->find($vendorId);
    return [
      'items' => $vendorItems->items,
      'units' => $units
    ];
  }

  public function deleteFile(Request $request)
  {

    $folder = $request->input('path');
    $fileName = $request->input('docId');
    // $name = $this->name('public/uploads/', $folder, $fileName);
    
    $path = explode('=', $fileName);
    
    $folderPath = 'public/uploads/'.$folder.'/'.$path[0].'/'.$path[1];
   
    // $folderPath = 'public/uploads/users/FMB_1_Developer/upload_national_identity/67143ece3f402_medical.png';

    if (Storage::exists($folderPath)) {
      Storage::delete($folderPath);
      return response()->json(['message' => 'Deleted successfully.'], 200);
    } else {
      return response()->json(['error' => 'File not found.'], 404);
    }
  }

  public function fetchVendorEventItems(Request $request)
{
    $eventIds = $request->event_ids ?? [];

    // Load all menus by event ID
    $menus = Menu::with([
        'recipes.recipeItems.item.purchaseOrderDetail',
        'recipes.recipeItems.measurement',
        'recipes.recipeItems.item',
        'event'
    ])->whereIn('event_id', $eventIds)->get();

    // Split menus
    $chefInputMenus = $menus->where('item_quantity', 'chef-input');
    $otherMenus = $menus->where('item_quantity', '!=', 'chef-input');

    /**
     * Handle Chef Input Menus
     */
    $chefItems = $chefInputMenus->flatMap(function ($menu) {
        // Only get recipes relevant to the menu
        $recipes = Recipe::with([
            'dish.dishCategory',
            'chefRecipeItems' => function ($query) use ($menu) {
                $query->where('menu_id', $menu->id)
                      ->with(['item.purchaseOrderDetail', 'measurement']);
            }
        ])->whereHas('chefRecipeItems', function ($query) use ($menu) {
            $query->where('menu_id', $menu->id);
        })->get();

        $servingQuantity = $recipes->sum(fn($recipe) => (float) $recipe->serving);

        return $recipes->flatMap(function ($recipe) use ($menu, $servingQuantity) {
            return $recipe->chefRecipeItems->map(function ($recipeItem) use ($menu) {
                return [
                    'event_id'      => $menu->event_id,
                    'event_name'    => optional($menu->event)->name,
                    'menu_id'       => $menu->id,
                    'item'          => $recipeItem->item,
                    'recipe_id'     => $recipeItem->recipe_id,
                    'item_quantity' => $recipeItem->item_quantity,
                    'unit'          => $recipeItem->measurement,
                ];
            });
        });
    });

    /**
     * Handle Other Menus (non-chef-input)
     */
    $events = Event::with(['menus.recipes', 'menus.menuServings.recipeItem.item', 'menus.menuServings.recipeItem.measurement'])
        ->whereIn('id', $eventIds)
        ->get();

    $otherItems = $events->flatMap(function ($event) {
        return $event->menus->flatMap(function ($menu) use ($event) {
            $servingQuantity = $menu->recipes->sum('serving');

            return $menu->menuServings->map(function ($serving) use ($event, $menu, $servingQuantity) {
                $recipeItem = $serving->recipeItem;

                if (!$recipeItem || !$recipeItem->item) return null;

                return [
                    'event_id'      => $event->id,
                    'event_name'    => $event->name,
                    'menu_id'       => $menu->id,
                    'item'          => $recipeItem->item,
                    'recipe_id'     => $recipeItem->recipe_id,
                    'item_quantity' => $servingQuantity > 0
                        ? ($event->serving_persons / $servingQuantity) * $recipeItem->item_quantity
                        : 0,
                    'unit'          => $recipeItem->measurement,
                ];
            })->filter();
        });
    });

    /**
     * Merge & Return
     */
    $items = $chefItems->merge($otherItems)->filter()->values();
    return response()->json($items);
}


  public function fetchUomBase(Request $request)
  {
    $itemId = $request->item;
    $selectedMeasure = $request->unit_measure;

    $itemBaseUom = ItemBaseUom::with("baseUom")->where("item_id", $itemId)->first();

    if (!$itemBaseUom) {
      return response()->json(['success' => false, 'message' => 'Base UOM not found for the given item.']);
    }
    $baseUOM = $itemBaseUom->unit_measure_id;

    if ($baseUOM ==  $selectedMeasure) {
      return response()->json(['success' => true, 'message' => 'Selected Measure is the base UOM']);
    }

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
    if ($checking) {
      return response()->json([
        'success' => true,
        'check' => $check,
      ]);
    } else {
      return response()->json([
        'success' => false,
        'check' => $check,
        'itemBaseUom' => $itemBaseUom,
        'conversion' => $checking
      ]);
    }
  }

  public function fetchReturnFromKitchenItems(Request $request)
  {
    $units = UnitMeasure::all();
    $results = Item::query()
    ->withWhereHas('inventoryDetails', function ($query) use ($request) {
      $query->with('returns')
      ->where('event_id', $request->event_id)
      ->where('kitchen_id', $request->kitchen_id);
    })
    ->get()
    ->filter(function ($item) {
      $issueQuantity = $item->inventoryDetails->sum('quantity');
      $returnQuantity = $item->inventoryDetails->sum(function ($detail) {
        return $detail->returns->sum('quantity');
      });
      $remainingIssueQuantity = $issueQuantity - $returnQuantity;
      return $remainingIssueQuantity > 0;
    });
    return [
      'items' => $results,
      'units' => $units
    ];
  }
  public function fetchEventItems(Request $request)
  {


    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'detail.approvedDetail.inventories.store.place')->where("status", "approved")->where("id", $request->input('event_id'))->first();

    $statuses = $result->detail->pluck('approvedDetail')
      ->pluck('inventory')
      ->flatten()
      ->map(fn($inventory) => $inventory ? $inventory->inventory_status : "Remaining");

    $allCompleted = $statuses->every(fn($status) => $status === "Completed");
    // dd($result->toArray(),$statuses,$allCompleted);
    return response()->json([
      'allCompleted' => $allCompleted,
      'purchaseOrder' => $result,
    ]);
  }
  public function fetchEventItemList(Request $request)
  {
    $eventId = $request->input('event_id');
    $itemId = $request->input('item_id');
    $query = PurchaseOrderDetail::withoutGlobalScope(OrderByIdDescScope::class)
    ->withWhereHas('approvedDetail')
    ->with(['item.itemBase.baseUom', 'selectedUnitMeasure'])
    ->where('event_id', $eventId);
    if ($itemId) {
      $query->where('item_id', $itemId);
    }
    $results = $query->get();
    if ($results->count() == 0) {
      return [];
    }
    $item = Item::withoutGlobalScope(OrderByIdDescScope::class)
    ->withWhereHas('inventoryDetails', function ($query) use ($eventId) {
      $query->with('returns')
      ->where('event_id', $eventId);
      // ->where('kitchen_id', $request->kitchen_id);
    })
    ->get()
    ->map(function ($item) {
      $issuedQuantity = $item->inventoryDetails->sum('quantity');
      $returnQuantity = $item->inventoryDetails->sum(function ($detail) {
        return $detail->returns->sum('quantity');
      });
      return [
        'item_id' => $item->id,
        'issued_quantity' => $issuedQuantity,
        'returned_quantity' => $returnQuantity
      ];
    });
    $issuedItemDetails = $results->map(function ($purchaseOrderDetail) use ($item) {
      $unitOptions = $this->fetchUnit(new Request(['id' => $purchaseOrderDetail->item->id]));
      $issuedQuantity = $item->where('item_id', $purchaseOrderDetail->item->id)->pluck('issued_quantity')->first() ?? 0;
      $returnQuantity = $item->where('item_id', $purchaseOrderDetail->item->id)->pluck('returned_quantity')->first() ?? 0;
      return [
        'id' => $purchaseOrderDetail->item->id,
        'name' => $purchaseOrderDetail->item->name,
        'uom' => $purchaseOrderDetail->selectedUnitMeasure->name,
        'uom_short' => $purchaseOrderDetail->selectedUnitMeasure->short_form,
        'uom_id' => $purchaseOrderDetail->selectedUnitMeasure->id,
        'unitOptions' => $unitOptions['result'],
        'issued_quantity' => $issuedQuantity - $returnQuantity,
        'remaining_quantity' => $purchaseOrderDetail->approvedDetail->quantity - ($issuedQuantity - $returnQuantity),
        'itemQuantity' => $purchaseOrderDetail->approvedDetail->quantity,
      ];
    })
    ->filter()->values();
    return $issuedItemDetails;
  }

  public function fetchItemDetails(Request $request)
  {
    $data = Item::query()
    ->withWhereHas('detail')
    ->withWhereHas('itemBase.baseUom')
    ->find($request->id);
    return $data;
    // ->withWh('itemBase.baseUom')
  }

  public function fetchReturnItems()
  {
    $units = UnitMeasure::all();
    $items = Item::withWhereHas('detail')
    ->get()
    ->filter(function ($item) {
      return $item->detail->available_quantity > 0;
    });
    return [
      'items' => $items,
      'units' => $units
    ];
  }

  public function fetchEventItemDetailsForKitchen(Request $request)
  {
    $item = Item::query()
    ->withWhereHas('inventoryDetails', function ($query) use ($request) {
      $query->with('returns')
      ->where('event_id', $request->event_id);
      // ->where('kitchen_id', $request->kitchen_id);
    })
    ->find($request->item_id);
    $issuedQuantity = $item->inventoryDetails->sum('quantity');
    $returnQuantity = $item->inventoryDetails->sum(function ($detail) {
      return $detail->returns->sum('quantity');
    });
    return [
      'inventory_detail_id' => $item->inventoryDetails->pluck('id')->first(),
      'id' => $item->id,
      'name' => $item->name,
      'issued_quantity' => $issuedQuantity - $returnQuantity,
      'base_uom' => $item?->itemBase?->baseUom
    ];
  }

  public function fetchAdjustmentItems()
  {
    $items = Item::withWhereHas('detail')
    ->get()
    ->filter(function ($item) {
      return $item->detail->available_quantity > 0;
    })
    ->values();
    $units = UnitMeasure::all();
    return [
      'items' => $items,
      'units' => $units
    ];
  }

  public function fetchGrnItems(Request $request)
  {
    $grnId = $request->grn_id;
    $units = UnitMeasure::all();
    $purchaseOrders = PurchaseOrder::with('vendor', 'detail.item.detail', 'detail.unitMeasure', 'currency')->find($grnId);
    $items = $purchaseOrders?->detail?->map(function ($item) {
      return $item?->item;
    })
    ->filter(function ($item) {
      return $item->detail->available_quantity > 0;
    });
    session()->put('supplier_return_items', $items);
    return [
      'items' => $items,
      'units' => $units,
      'vendor' => $purchaseOrders?->vendor
    ];
  }


  function getServingQuantityIdsWithinCurrentMonth(Request $request)
  {
    
    $year = $request->year;
    $month = $request->month;
    $totalDays = Carbon::create($year, $month)->daysInMonth;

    $monthInput = $request->value; // "2028-06"
    $monthCarbon = Carbon::createFromFormat('Y-m', $monthInput);
    $formattedMonth = $monthCarbon->format('M'); // Output: "Jun"
    
    function getOrdinalSuffix($i) {
      if (!in_array(($i % 100), [11, 12, 13])) {
      switch ($i % 10) {
      case 1: return 'st';
      case 2: return 'nd';
      case 3: return 'rd';
      }
      }
      return 'th';
    }
    $currentMonth = [];
    for ($i = 1; $i <= $totalDays; $i++) {
      $results = ServingQuantity::with("servingQuantityItems")->get();
      $currentDate = Carbon::createFromDate($year, $month, $i)->toDateString();
      $dayData = [
        "date" => $i,
        'term' =>getOrdinalSuffix($i),
        "month" => $formattedMonth,
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
}
