<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Inventory\AddRequest;
use App\Http\Requests\Inventory\UpdateRequest;
use App\Http\Requests\Inventory\SupplierReturnRequest;
use App\Http\Requests\Inventory\AdjustmentRequest;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\PurchaseOrder;
use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Place;
use App\Models\Store;
use App\Models\UnitMeasure;
use App\Models\Kitchen;
use App\Models\Event;
use App\Models\GoodIssue;
use App\Models\PurchaseOrderDetail;
use App\Models\User;
use App\Models\InventoryDetail;
use App\Models\InventoryDetailReturn;
use App\Models\ItemBaseUom;
use App\Models\Menu;
use App\Models\SupplierReturn;
use App\Models\UomConversion;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = Item::query()
    ->with([
      'itemCategory', 'itemBase.baseUom', 'purchaseOrderDetails.approvedDetail.inventories',
      'inventoryDetails.returns',
      'supplierReturn',
      'inventoryDetail',
      'supplierReturns'
    ])
    ->get()
    ->map(function ($result) {

      $inventoryQuantity = $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
        return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
      });
      $issuedQuantity = $result->inventoryDetails->sum(function ($inventoryDetail) {
        return $inventoryDetail->quantity ?? 0;
      });
      $returnedQuantity = $result->inventoryDetails->sum(function ($inventoryDetail) {
        return collect($inventoryDetail->returns ?? [])->sum('quantity');
      });
      $supplierReturnedQuantity = $result->supplierReturns->sum(function ($supplierReturn) {
        return $supplierReturn?->quantity ?? 0;
      });
      return [
        'id' => $result->id,
        'name' => $result->name,
        'uom' => $result->itemBase->baseUom->short_form,
        'itemCategoryName' => $result->itemCategory->name,
        'total_quantity' => $inventoryQuantity,
        'issued_quantity' => $issuedQuantity,
        'returned_quantity' => $returnedQuantity,
        'inventory_detail' => $result->inventoryDetail,
        'supplier_return' => $result->supplierReturn,
        'remaining_quantity' => ($inventoryQuantity - $issuedQuantity - $supplierReturnedQuantity) + $returnedQuantity,
        'stores' => $result->purchaseOrderDetails->flatMap(function ($purchaseOrderDetail) {
          return $purchaseOrderDetail->approvedDetail?->inventories->map(function ($inventory) {
            return $inventory->store->floor ?? '-';
          });
        })->unique()->implode(',<br/> ')
      ];
    });
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $users = User::whereHas('roles', function ($query) {
      $query->whereIn('name', ['Store-Manager', 'Chef', 'Chef-Worker']);
    })->select('users.*')->with('roles')->get();
    $kitchens = Kitchen::with('place')->get();
    $stores = Store::with('place')->get();
    $events = Event::whereHas('menus')->where("status", "Approved")->get();
    $itemsCount = Item::count();
    $results = [];
    return view($this->view, compact('results', 'itemsCount', 'users', 'kitchens', 'events', 'stores'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {
    $validated = $request->validated();
    // dd($validated);
    try {
      if (!empty($request->worker_name)) {

        $email = strtolower($request->worker_name) . '@yopmail.com'; // Ensure case insensitivity

        // Check if the user already exists
        $user = User::where('email', $email)->first();

        if (!$user) {
          // Generate a strong random password
          $hashedPassword = bcrypt('asdwe2134@1');

          // Create a new user
          $user = User::createWithTransaction([
            'name' => $request->worker_name,
            'email' => $email,
            'password' => $hashedPassword,
          ]);

          if (!$user) {
            throw new Exception("User creation failed.");
          }

          // Assign role
          $user->assignRole('chef-worker');
        }
        $validated['received_by'] = $user->id;
      }

      DB::beginTransaction();
      if (isset($validated['other']) && $validated['other']  != "0") {

        $results = Item::query()
          ->with(['itemBase.baseUom'])
          ->withWhereHas('detail')
          ->withWhereHas('purchaseOrderDetails.approvedDetail.inventories')
          ->pluck('id')
          ->toArray();
        foreach ($validated['other'] as $key => $value) {
          if (!in_array($key, $results)) {
            // dd($validated['other'],$key,empty($purchaseOrderDetail),$purchaseOrderDetail);
            $event = Event::where("id", $validated['event_id'])->first();
            $purchaseOrder = PurchaseOrder::createWithTransaction([
              "place_id" => $event->place_id,
              "status" => "approved",
              "approved_by" => Auth::id(),
              'type' => 'gin'
            ]);

            $item = Item::with('itemBase')->find($key);
            $itemBaseUnitId = $item->itemBase->unit_measure_id;

            $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
              'purchase_order_id' => $purchaseOrder->id,
              'item_id' => $key,
              'unit_measure_id' => $itemBaseUnitId,
              'select_unit_measure_id' => $validated['unit_id'][$key],
              'select_quantity' => 0,
              'quantity' => 0,
            ]);
            $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::createWithTransaction([
              'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
              'quantity' => 0,
            ]);
            Inventory::createWithTransaction([
              'approved_purchase_order_detail_id' => $approved_purchase_order_detail->id,
              'quantity' => 0,
              'remaining' => 0,
              'inventory_status' => 'Completed',
              'store_id' => $validated['store_id'],
            ]);
          }
        }
      }

      if (isset($validated['issue_unit'])) {
        $goodIssue = GoodIssue::createWithTransaction(
          [
            'event_id' => $validated['event_id'],
            'kitchen_id' => $validated['kitchen_id'],
          ]
        );
        $validated['good_issue_id'] = $goodIssue->id;

        foreach ($validated['quantity'] as $key => $value) {
          $item = Item::with('itemBase')->find($key);
          $itemBaseUnitId = $item->itemBase->unit_measure_id;

          $data = $this->issuedDataToCreate($validated, $itemBaseUnitId, ($itemBaseUnitId == ($validated['issue_unit'][$key] ?? $validated['unit_id'][$key]) ? true : false), $key, $value);

          InventoryDetail::createWithTransaction($data);

          updateItemDetails(
            $key,
            [
              'issued_quantity' => $data['quantity']
            ]
          );
        }
      }
      DB::commit();
      return redirect()->route('issued-to-kitchens.index')->with('success', '' . $this->controller . ' created successfully.');
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
    // try {

      $item = Item::query()
        ->with('itemBase.baseUom')
        ->findOrFail($id);

      $data = Item::with(
        'itemBase.baseUom',
        'purchaseOrderDetails.approvedDetail.inventories.createdBy',
        'purchaseOrderDetails.approvedDetail.inventories.store.place',
        'purchaseOrderDetails.event',
        'purchaseOrderDetails.purchaseOrder.store.place',
        'inventoryDetails.returns',
        'inventoryDetails.createdBy',
        'inventoryDetails.receivedBy',
        'inventoryDetails.event',
        'inventoryDetails.kitchen',
        'inventoryDetails.store.place'
      )->where("id", $id)->first();

      // dd($data->toArray());

      $itemReturn = SupplierReturn::with('createdBy', 'vendor')
        ->where("item_id", $id)
        ->get()
        ->map(function ($item) {
          $item->action = $item->vendor == null ? 'adjustment' : 'Supplier Return';
          return $item;
        });
      // dd($itemReturn->toArray());
      if ($data) {
        $results = [
          'id' => $data->id,
          'name' => $data->name,
          'data' => collect(array_merge(
            // Process inventories
            $data->purchaseOrderDetails
              ->flatMap(function ($detail) {
                return collect($detail->approvedDetail->inventories ?? [])
                  ->map(function ($inventory) use ($detail) {
                    return array_merge($inventory->toArray(), [
                      'action' => 'received',
                      'type' => $detail->purchaseOrder->type,
                      'place_name' =>  $detail->purchaseOrder->store->place->name ?? "-",
                      'detail' => ['event' => $detail->event],
                    ]);
                  });
              })->reverse()
              ->toArray(),

            // Process received
            $data->inventoryDetails
              ->map(function ($detail) {
                return array_merge($detail->toArray(), [
                  'type' => $detail->event == null ? 'transfer' : '',
                  'place_name' =>  $detail->store->place->name ?? "-",
                  'action' => 'issued',
                ]);
              })->reverse()
              ->toArray(),

            // Add itemReturn with mapped action data
            $itemReturn->map(function ($returnItem) {
              return $returnItem->toArray();
            })->toArray(),

            // Process returns
            $data->inventoryDetails
              ->flatMap(function ($detail) {
                return $detail->returns->map(function ($return) use ($detail) {
                  return array_merge($return->toArray(), [
                    'action' => 'Return',
                    'detail' => $detail->toArray(),
                  ]);
                });
              })->reverse()
              ->toArray()
          ))->sortByDesc('created_at') // Sort by created_at in descending order
            ->values()  // Re-index the collection after sorting
            ->toArray(),
        ];
      } else {
        $results = null; // Handle case where item is not found
      }
      // return $results;
      // dd($results, $itemReturn->toArray());


      return view($this->view, compact('results', 'item'));
    // } catch (\Throwable $th) {
    //   return redirect('inventories');
    // }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $users = User::all();
    $result = Item::query()
    ->with([
      'itemBase.baseUom', 'purchaseOrderDetails.approvedDetail.inventories',
      'inventoryDetails.returns'
    ])
    ->findOrFail($id);
    $inventoryQuantity = $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
      return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
    });
    $issuedQuantity = $result->inventoryDetails->sum(function ($inventoryDetail) {
      return $inventoryDetail->quantity ?? 0;
    });
    $returnedQuantity = $result->inventoryDetails->sum(function ($inventoryDetail) {
      return collect($inventoryDetail->returns ?? [])->sum('quantity');
    });
    $unitOptions = $this->fetchUnit(new Request(['id' => $id]));
    $unitOptions = $unitOptions['result'];
    $kitchens = Kitchen::all();
    $stores = Store::with('place')->get();
    $events = Event::whereHas('menus')->where("status", "Approved")->get();
    $inventoryQuantity = $inventoryQuantity - ($issuedQuantity - $returnedQuantity);
    return view($this->view, compact('result', 'stores', 'unitOptions', 'id', 'kitchens', 'users', 'events', 'inventoryQuantity'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateRequest $request, string $id)
  {
    $validated = $request->validated();
    $validated = Arr::except($validated, ['inventory_quantity']);
    $validated['item_id'] = $id;
    try {
      DB::beginTransaction();

      if (!empty($request->worker_name)) {
        $email = strtolower($request->worker_name) . '@yopmail.com'; // Ensure case insensitivity

        // Check if the user already exists
        $user = User::where('email', $email)->first();

        if (!$user) {
          // Generate a strong random password
          $hashedPassword = bcrypt('asdwe2134@1');

          // Create a new user
          $user = User::createWithTransaction([
            'name' => $request->worker_name,
            'email' => $email,
            'password' => $hashedPassword,
          ]);

          if (!$user) {
            throw new Exception("User creation failed.");
          }

          // Assign role
          $user->assignRole('chef-worker');
        }
        $validated['received_by'] = $user->id;
      }

      $item = Item::with('itemBase')->find($id);
      $itemBaseUnitId = $item->itemBase->unit_measure_id;

      $goodIssue = GoodIssue::createWithTransaction(
        [
          'event_id' => $validated['event_id'],
          'kitchen_id' => $validated['kitchen_id'],
        ]
      );
      $validated['good_issue_id'] = $goodIssue->id;

      $data = $this->issuedDataToUpdate($validated, $itemBaseUnitId, ($itemBaseUnitId == ($request['issue_unit'] ?? $request['unit_id']) ? true : false), $id, $validated['quantity']);

      InventoryDetail::createWithTransaction($data);
      updateItemDetails(
        $id,
        [
          'issued_quantity' => $validated['quantity']
        ]
      );
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function destroy(string $id) {}

  public function transfer(string $id)
  {

    try {
      $item = Item::query()
        ->with(['itemBase.baseUom', 'itemCategory'])
        ->withWhereHas('detail')
        ->withWhereHas('purchaseOrderDetails.approvedDetail.inventories.store.place')
        ->where("id", $id)
        ->first();

      if (!$item) {
        return null; // or handle not found case
      }
      $result = Item::with([
        'itemCategory',
        'itemBase.baseUom',
        'purchaseOrderDetails.approvedDetail.inventories.store.place',
        'inventoryDetails.returns',
        'supplierReturn',
      ])
        ->where('id', $id)
        ->first();

      if (!$result) {
        abort(404, 'Item not found');
      }

      // Calculate total received quantity grouped by store
      $receivedPerStore = $result->purchaseOrderDetails->flatMap(function ($pod) {
        return collect($pod->approvedDetail?->inventories ?? []);
      })->groupBy(fn($inventory) => $inventory->store->id ?? null)
        ->map(fn($inventories) => $inventories->sum('quantity'));

      // Calculate issued quantity grouped by store_id from inventoryDetails (plural assumed)
      $issuedPerStore = $result->inventoryDetails
        ->groupBy('store_id')
        ->map(fn($details) => $details->sum('quantity'));

      // Prepare stores data
      $stores = $receivedPerStore->map(function ($received, $storeId) use ($issuedPerStore, $result) {
        $issued = $issuedPerStore->get($storeId, 0);
        $available = $received - $issued;

        $store = null;
        foreach ($result->purchaseOrderDetails as $pod) {
          foreach ($pod->approvedDetail?->inventories ?? [] as $inventory) {
            if (($inventory->store->id ?? null) === $storeId) {
              $store = $inventory->store;
              break 2;
            }
          }
        }

        $placeName = $store->place->name ?? '-';
        $storeName = $store->floor ?? '-';

        return [
          'store_id' => $storeId,
          'place_name' => $placeName,
          'store_name' => $storeName,
          'received' => $received,
          'issued' => $issued,
          'available' => $available,
          'minus_value' => $available < 0 ? 'true' : 'false',
        ];
      })->values()->all();

      // Calculate totals
      $totalReceived = $receivedPerStore->sum();
      $totalIssued = $issuedPerStore->sum();
      $totalAvailable = $totalReceived - $totalIssued;

      // Prepare final data array
      $result = [
        'id' => $result->id,
        'name' => $result->name,
        // Use 'short_form' if it exists; otherwise fallback to 'uom' string
        'uom' => $result->itemBase->baseUom->short_form ?? $result->uom ?? '-',
        'itemCategoryName' => $result->itemCategory->name ?? '-',
        'total_quantity' => $totalReceived,
        'issued_quantity' => $totalIssued,
        'returned_quantity' => $result->inventoryDetails->sum(function ($inventoryDetail) {
          return collect($inventoryDetail->returns ?? [])->sum('quantity');
        }),
        'inventory_detail' => $result->inventoryDetails,  // use plural
        'supplier_return' => $result->supplierReturn,
        'remaining_quantity' => $totalAvailable,
        'stores' => $stores,
      ];

      $stores = Store::get();
      // dd($result);
      $fetchUnits = ItemBaseUom::with(['item', 'unitMeasure', 'baseUom'])
        ->where('item_id', $id)
        ->get()
        ->flatMap(function ($itemBaseUom) {
          $baseUomCollection = $itemBaseUom->baseUom ? collect([$itemBaseUom->baseUom]) : collect();
          $mergedUnitMeasures = $baseUomCollection->merge($itemBaseUom->unitMeasure);
          $unitMeasures = $mergedUnitMeasures;
          return $unitMeasures->map(function ($unitMeasure) {
            return [
              'id' => $unitMeasure->id,
              'name' => $unitMeasure->name,
              'short_form' => $unitMeasure->short_form,
            ];
          });
        });

      return view($this->view, compact('result', 'item', 'stores', 'fetchUnits'));
    } catch (\Throwable $th) {
      return redirect('inventories');
    }
  }
  public function storeTransfer(Request $request, string $id)
  {
    // dd($request->all(),$id);
    DB::beginTransaction();
    try {
       // issue start
        $goodIssue = GoodIssue::create(
          [
            'created_by' =>Auth::id()
          ]
        );

        $data = [
          'store_id' => $request->store_id,
          'received_by' => Auth::id(),
          'good_issue_id' => $goodIssue->id,
          'item_id' => $id,
          'quantity' => $request->input('quantity'),
          'select_quantity' => $request->input('quantity'),
          'select_unit_measure_id' => $request->input('base_uom'),
          'unit_measure_id' => $request->input('base_uom'),
        ];
        InventoryDetail::createWithTransaction($data);

        updateItemDetails(
          $id,
          [
            'issued_quantity' => $request->input('quantity')
          ]
        );
        // issue End

        // Grn Start
        if($request->quantity && $request->other_store_id) {
          
          $purchaseOrder = PurchaseOrder::createWithTransaction([
            "place_id" => Auth::user()->place_id,
            "store_id" => $request->other_store_id,
            "status" => "approved",
            "approved_by" => Auth::id(),
            'type' => 'transfer',
            'currency_id' => 2
          ]);

          $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
            'purchase_order_id' => $purchaseOrder->id,
            'item_id' => $id,
            'unit_measure_id' => $request->input('base_uom'),
            'select_unit_measure_id' => $request->input('base_uom'),
            'select_quantity' => $request->quantity,
            'quantity' => $request->quantity,
            'unit_price' => 0,
          ]);
          $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::createWithTransaction([
            'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
            'quantity' => $request->quantity,
          ]);
          Inventory::createWithTransaction([
            'approved_purchase_order_detail_id' => $approved_purchase_order_detail->id,
            'quantity' => $request->quantity,
            'remaining' => 0,
            'inventory_status' => 'Completed',
            'store_id' => $request->other_store_id,
          ]);

          updateItemDetails(
            $id,
            [
              'received_quantity' => $request->quantity
            ]
          );
        }

        // Grn End
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }
  public function editInventory(string $id)
  {
    try {
    $item = Item::query()
      ->with([
        'itemCategory',
        'itemBase.baseUom',
        'purchaseOrderDetails.approvedDetail.inventories.store.place',
        'inventoryDetails.returns',
        'supplierReturn',
        'inventoryDetail'
      ])
      ->withWhereHas('detail')
      ->where('id', $id)
      ->first();

    if (!$item) {
      redirect()->route($this->redirect)->with('error', 'Item Not Found');
    }

    // Calculate total received, issued, returned
    $inventoryQuantity = $item->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
      return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
    });

    $issuedQuantity = $item->inventoryDetails->sum(function ($inventoryDetail) {
      return $inventoryDetail->quantity ?? 0;
    });

    $returnedQuantity = $item->inventoryDetails->sum(function ($inventoryDetail) {
      return collect($inventoryDetail->returns ?? [])->sum('quantity');
    });

    // Per-store details
    $storeQuantities = $item->purchaseOrderDetails
      ->flatMap(function ($purchaseOrderDetail) {
        return $purchaseOrderDetail->approvedDetail?->inventories ?? collect();
      })
      ->filter()
      ->groupBy(fn($inventory) => $inventory->id ?? '-')
      ->map(function ($groupedInventories) use ($item) {
        $firstInventory = $groupedInventories->first();
        $inventory =  $firstInventory->id;
        $approved_purchase_order_detail_id =  $firstInventory->approved_purchase_order_detail_id;
        $inventoryDetailSum = InventoryDetail::where('item_id', $item->id)
          ->sum('quantity');

        $available = $groupedInventories->sum('quantity') - $inventoryDetailSum;

        // Find matching purchase order detail
        $unit = $item->purchaseOrderDetails->filter(function ($purchaseOrderDetail) use ($approved_purchase_order_detail_id) {
          return optional($purchaseOrderDetail->approvedDetail)->id == $approved_purchase_order_detail_id;
        })->first();
        return [
          'store_id' => $firstInventory->store->id,
          'place_id' => $firstInventory->store->place->id,
          'store_name' => ($firstInventory->store->place->name ?? '-') . ' - ' . ($firstInventory->store->floor  ?? "-"),
          // 'price' => ($unit->total != 0 && $unit->quantity > 0)
          //   ? round($unit->total / $unit->quantity, 2)
          //   : $unit->unit_price,
          'price' => ($unit && $unit->total != 0 && $unit->quantity > 0)
          ? round($unit->total / $unit->quantity, 2)
          : ($unit->unit_price ?? 0),
          // 'price' => $unit->unit_price,
          'inventory_id' => $inventory,

          'purchase_order_id' => $unit->purchase_order_id ?? '',
          'received' => $groupedInventories->sum('quantity'),
          'issued' => $inventoryDetailSum,
          'available' => $available,
          'minus_value' => $available < 0 ? 'true' : 'false',

        ];
      })
      ->values();

    $result = [
      'id' => $item->id,
      'name' => $item->name,
      'uom' => $item->itemBase->baseUom->name,
      'short_uom' => $item->itemBase->baseUom->short_form,
      'itemCategoryName' => $item->itemCategory->name,
      'total_quantity' => $inventoryQuantity,
      'issued_quantity' => $issuedQuantity,
      'returned_quantity' => $returnedQuantity,
      'remaining_quantity' => $inventoryQuantity - ($issuedQuantity - $returnedQuantity),
      'inventory_detail' => $item->inventoryDetail,
      'supplier_return' => $item->supplierReturn,
      'stores' => $storeQuantities->toArray(),
    ];

    $stores = $result['stores'];
    $uniquePOCount = collect($result['stores'])->pluck('purchase_order_id')->unique()->count();

    $fetchUnits = ItemBaseUom::with(['item', 'unitMeasure', 'baseUom'])
      ->where('item_id', $id)
      ->get()
      ->flatMap(function ($itemBaseUom) {
        $baseUomCollection = $itemBaseUom->baseUom ? collect([$itemBaseUom->baseUom]) : collect();
        $mergedUnitMeasures = $baseUomCollection->merge($itemBaseUom->unitMeasure);
        $unitMeasures = $mergedUnitMeasures;
        return $unitMeasures->map(function ($unitMeasure) {
          return [
            'id' => $unitMeasure->id,
            'name' => $unitMeasure->name,
            'short_form' => $unitMeasure->short_form,
          ];
        });
      });

    return view($this->view, compact('result', 'item', 'stores', 'fetchUnits','uniquePOCount'));
    } catch (\Throwable $th) {
      return redirect('inventories');
    }
  }
  public function updateInventory(Request $request, string $id)
  {
    DB::beginTransaction();
    try {
      $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_Id);
      $purchaseOrderDetail = PurchaseOrderDetail::where([
        'purchase_order_id' => $purchaseOrder->id,
        'item_id' => $id,
      ])->firstOrFail();
      
      
      $oldAmount = $purchaseOrder->amount;
      $oldQuantity = $request->available_quantity;
      $oldUnitPrice = $request->unit_price;
      $newQuantity = $request->update_quantity;
      $newUnitPrice = $request->update_unit_price;
      // $newSellingPrice = $request->update_selling_price;
      
      $oldTotal = $oldQuantity * $oldUnitPrice;
      $newTotal = $newQuantity * $newUnitPrice;
      $delta = $newTotal - $oldTotal;
      
      $finalAmount = $oldAmount != 0 ? $oldAmount + $delta : $newTotal;
      
      if ($request->items == 1) {
        $finalQuantity = $newQuantity;
      } else {
        $finalQuantity = $purchaseOrderDetail->quantity - $oldQuantity + $newQuantity;
      }

        //  dd([
        //     'parentOldAmount' => $oldAmount,
        //     'parentNewAmount' => $finalAmount,

        //     'oldChildTotal'=> $oldTotal,
        //     'newChildTotal' => $newTotal,

        //     'oldUnitPrice' => $oldUnitPrice,
        //     'newUnitPrice' => $newUnitPrice,
            
        //     'old_child_q' => $oldQuantity,
        //     'new_child_q' => $newQuantity,
        //     'final_quantity' => $finalQuantity,
            
        //     'end' => '-----------------------------',

        //   "purchaseOrder" => $purchaseOrder->id,
        //   'amount' => $finalAmount,

        //   "purchaseOrderDetail" => $purchaseOrderDetail->id,
        //   'select_quantity' => $finalQuantity,
        //   'quantity' => $finalQuantity,
        //   'unit_price' => $newUnitPrice,
        //   'per_unit_selling' => $newSellingPrice,
        //   // 'total' => $newTotal,
        //   'total' => $request->same == 1 ? $finalAmount + ($purchaseOrder->discount ?? 0) : $newTotal,
          
        //   'approvedDetail' => '-',
        //   'approvedDetail_quantity' => $finalQuantity,
        //   'approvedDetail_total' => $request->same == 1 ? $finalAmount + ($purchaseOrder->discount ?? 0) : $newTotal,

        //   'Inventory' => '-',
        //   'Inventory_quantity' => $newQuantity,
        //   'updated_by' => Auth::id(),
        // ]);

      // Update Purchase Order
      $purchaseOrder->update([
        "amount" => $finalAmount,
        "updated_by" => Auth::id(),
      ]);
      
      // Update Purchase Order Detail
      $purchaseOrderDetail->update([
        'select_quantity' => $finalQuantity,
        'quantity' => $finalQuantity,
        'unit_price' => $newUnitPrice,
        // 'per_unit_selling' => $newSellingPrice,
        'total' => $request->same == 1 ? $finalAmount + ($purchaseOrder->discount ?? 0) : $newTotal,
      ]);
      
      // Update Approved Purchase Order Detail
      $approvedDetail = ApprovedPurchaseOrderDetail::where([
        'purchase_order_detail_id' => $purchaseOrderDetail->id,
      ])->firstOrFail();
      
      $approvedDetail->update([
        'quantity' => $finalQuantity,
        'total' => $request->same == 1 ? $finalAmount + ($purchaseOrder->discount ?? 0) : $newTotal,
        'updated_by' => Auth::id(),
      ]);
      
      // Update Inventory if exists
      if ($request->inventory_id) {
        Inventory::where('id', $request->inventory_id)->update([
          'quantity' => $newQuantity,
          'updated_by' => Auth::id(),
        ]);
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function add(string $id)
  {
    $stores = store::get();
    $results = Item::get();
    return view($this->view, compact('results', 'stores'));
  }
  public function storeAdd(Request $request)
  {
    // dd($request->all());
    DB::beginTransaction();
    try {
      $purchaseOrderItemsData = $request->input("quantity");
      $store_id = $request->input("store_id");
      $unit_id = $request->input("unit_id");

      $store = Store::find($store_id);
      $place = Place::find($store->place_id);
      // dd($request->all(),$place->id);

      $purchaseOrder = PurchaseOrder::createWithTransaction([
        "place_id" => $place->id,
        "status" => "approved",
        "approved_by" => Auth::id(),
        'type' => 'add'
      ]);

      foreach ($purchaseOrderItemsData as $key => $value) {

        $item = Item::with('itemBase')->find($key);
        $itemBaseUnitId = $item->itemBase->unit_measure_id;
        $quantityValue = $this->dataQuantity($request, $itemBaseUnitId, ($itemBaseUnitId == $unit_id[$key] ? true : false), $key, $value);

        $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
          'purchase_order_id' => $purchaseOrder->id,
          'item_id' => $key,
          'unit_measure_id' => $itemBaseUnitId,
          'select_unit_measure_id' => $unit_id[$key],
          'select_quantity' => $value,
          'quantity' => $quantityValue['quantity'],
        ]);
        $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::createWithTransaction([
          'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
          'quantity' => $quantityValue['quantity'],
        ]);
        Inventory::createWithTransaction([
          'approved_purchase_order_detail_id' => $approved_purchase_order_detail->id,
          'quantity' => $quantityValue['quantity'],
          'remaining' => 0,
          'inventory_status' => 'Completed',
          'store_id' => $store_id,
        ]);
        updateItemDetails(
          $key,
          [
            'received_quantity' => $quantityValue['quantity']
          ]
        );
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }
  public function supplierReturn()
  {
    $vendors = Vendor::all();
    $items = Item::all();
    return view($this->view, compact('vendors', 'items'));
  }
  public function fetchUnit(Request $request)
  {

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

    return [
      'result' => $results
    ];
  }

  public function storeSupplierReturn(SupplierReturnRequest $request)
  {
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $vendorId = $validated['vendor_id'];
      foreach ($validated['items'] as $key => $itemData) {
        $item = Item::with('itemBase')->find($itemData['item_id']);
        $itemBaseUnitId = $item->itemBase->unit_measure_id;
        $data = $this->dataToCreate($vendorId, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
        $data['reason'] = $itemData['reason'];
        if ($data['quantity'] > $itemData['available_quantity']) {
          DB::rollback();
          return redirect()->back()->withInput()->with('error', 'Converted return quantity ' . $data['quantity'] . ' cannot be greater than available quantity ' . $itemData['available_quantity']);
        }
        SupplierReturn::createWithTransaction($data);
        updateItemDetails(
          $itemData['item_id'],
          [
            'supplier_returned_quantity' => $data['quantity']
          ]
        );
      }
      DB::commit();
      return redirect()->route('inventories.index')->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function adjustment()
  {
    $results = Item::query()
      ->withWhereHas('detail')
      ->with('itemBase', function ($query) {
        $query->with(['baseUom', 'unitMeasure']);
      })
      ->get()
      ->map(function ($item) {
        $item->itemBase->unitMeasure = collect([$item->itemBase->baseUom])->merge($item->itemBase->unitMeasure);
        return $item;
      });
    return view($this->view, compact('results'));
  }

  public function storeAdjustment(AdjustmentRequest $request)
  {
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      foreach ($validated['items'] as $key => $itemData) {
        $itemBaseUnitId = $itemData['base_unit_id'];
        $data = $this->dataToCreate(null, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
        $data['reason'] = $itemData['reason'];
        if ($data['quantity'] > $itemData['available_quantity']) {
          DB::rollback();
          return redirect()->back()->with('error', 'Converted return quantity ' . $data['quantity'] . ' cannot be greater than available quantity ' . $itemData['available_quantity']);
        }
        SupplierReturn::createWithTransaction($data);
        updateItemDetails(
          $itemData['item_id'],
          [
            'adjusted_quantity' => $data['quantity']
          ]
        );
      }
      DB::commit();
      return redirect()->route('inventories.index')->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  private function dataToCreate($vendorId = null, $itemData, $itemBaseUnitId, $unitMatched)
  {
    if ($unitMatched) {
      $data = [
        'item_id' => $itemData['item_id'],
        'unit_measure_id' => $itemData['unit_id'],
        'quantity' => $itemData['quantity'],
      ];
    } else {
      $check = 1;
      $checking = UomConversion::where('base_uom', $itemBaseUnitId)
        ->where('secondary_uom', $itemData['unit_id'])
        ->first();
      if (!$checking) {
        $checking = UomConversion::where('base_uom', $itemData['unit_id'])
          ->where('secondary_uom', $itemBaseUnitId)
          ->first();
        $check = 2;
      }
      $convertedQuantity = $check == 1
        ? (float)(1 / $checking->conversion_value) * (float)$itemData['quantity']
        : (float)$checking->conversion_value * (float)$itemData['quantity'];
      $data = [
        'item_id' => $itemData['item_id'],
        'select_unit_measure_id' => $itemData['unit_id'],
        'unit_measure_id' => $itemBaseUnitId,
        'select_quantity' => $itemData['quantity'],
        'quantity' => $convertedQuantity,
      ];
    }
    if ($vendorId) {
      $data['vendor_id'] = $vendorId;
    }
    return $data;
  }

  private function issuedDataToCreate($itemData, $itemBaseUnitId, $unitMatched, $item_id, $quantity)
  {
    if ($unitMatched) {
      $data = [
        'event_id' => $itemData['event_id'],
        'kitchen_id' => $itemData['kitchen_id'],
        'store_id' => $itemData['store_id'],
        'received_by' => $itemData['received_by'],
        'good_issue_id' => $itemData['good_issue_id'],
        'item_id' => $item_id,
        'quantity' => $quantity,
        'select_quantity' => $quantity,
        'select_unit_measure_id' => $itemBaseUnitId,
        'unit_measure_id' => $itemBaseUnitId,
      ];
    } else {
      $check = 1;
      $checking = UomConversion::where('base_uom', $itemBaseUnitId)
        ->where('secondary_uom', (int) ($itemData['issue_unit'][$item_id] ?? $itemData['unit_id'][$item_id] ?? 0))
        ->first();

      if (!$checking) {
        $checking = UomConversion::where('base_uom', (int) ($itemData['issue_unit'][$item_id] ?? $itemData['unit_id'][$item_id] ?? 0))
          ->where('secondary_uom', $itemBaseUnitId)
          ->first();
        $check = 2;
      }

      $convertedQuantity = $check == 1
        ? (float)(1 / $checking->conversion_value) * (float)$quantity
        : (float)$checking->conversion_value * (float)$quantity;

      $data = [
        'event_id' => $itemData['event_id'],
        'kitchen_id' => $itemData['kitchen_id'],
        'store_id' => $itemData['store_id'],
        'received_by' => $itemData['received_by'],
        'good_issue_id' => $itemData['good_issue_id'],
        'item_id' => $item_id,
        'quantity' => $convertedQuantity,

        'select_quantity' => $quantity,
        'select_unit_measure_id' => (int) ($itemData['issue_unit'][$item_id] ?? $itemData['unit_id'][$item_id] ?? 0),
        'unit_measure_id' => $itemBaseUnitId,
      ];
    }
    return $data;
  }
  private function issuedDataToUpdate($itemData, $itemBaseUnitId, $unitMatched, $item_id, $quantity)
  {
    if ($unitMatched) {
      $data = [
        'event_id' => $itemData['event_id'],
        'kitchen_id' => $itemData['kitchen_id'],
        'store_id' => $itemData['store_id'],
        'received_by' => $itemData['received_by'],
        'good_issue_id' => $itemData['good_issue_id'],
        'item_id' => $item_id,
        'quantity' => $quantity,
        'select_quantity' => $quantity,
        'select_unit_measure_id' => $itemBaseUnitId,
        'unit_measure_id' => $itemBaseUnitId,
      ];
    } else {
      $check = 1;
      $checking = UomConversion::where('base_uom', $itemBaseUnitId)
        ->where('secondary_uom', (int) ($itemData['issue_unit'] ?? $itemData['unit_id'] ?? 0))
        ->first();

      if (!$checking) {
        $checking = UomConversion::where('base_uom', (int) ($itemData['issue_unit'] ?? $itemData['unit_id'] ?? 0))
          ->where('secondary_uom', $itemBaseUnitId)
          ->first();
        $check = 2;
      }

      $convertedQuantity = $check == 1
        ? (float)(1 / $checking->conversion_value) * (float)$quantity
        : (float)$checking->conversion_value * (float)$quantity;

      $data = [
        'event_id' => $itemData['event_id'],
        'kitchen_id' => $itemData['kitchen_id'],
        'store_id' => $itemData['store_id'],
        'received_by' => $itemData['received_by'],
        'good_issue_id' => $itemData['good_issue_id'],
        'item_id' => $item_id,
        'quantity' => $convertedQuantity,

        'select_quantity' => $quantity,
        'select_unit_measure_id' => (int) ($itemData['issue_unit'] ?? $itemData['unit_id'] ?? 0),
        'unit_measure_id' => $itemBaseUnitId,
      ];
    }
    return $data;
  }
  private function dataQuantity($itemData, $itemBaseUnitId, $unitMatched, $item_id, $quantity)
  {

    if ($unitMatched) {
      $data = [
        'quantity' => $quantity,
      ];
    } else {
      $check = 1;
      $checking = UomConversion::where('base_uom', $itemBaseUnitId)
        ->where('secondary_uom', (int)$itemData['unit_id'][$item_id])
        ->first();

      if (!$checking) {
        $checking = UomConversion::where('base_uom', (int)$itemData['unit_id'][$item_id])
          ->where('secondary_uom', $itemBaseUnitId)
          ->first();
        $check = 2;
      }
      $convertedQuantity = $check == 1
        ? (float)(1 / $checking->conversion_value) * (float)$quantity
        : (float)$checking->conversion_value * (float)$quantity;

      $data = [
        'quantity' => $convertedQuantity,
      ];
    }
    // dd($data);
    return $data;
  }
}
