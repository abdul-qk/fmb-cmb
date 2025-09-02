<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GoodReturnedNote\AddRequest;
use App\Http\Requests\GoodReturnedNote\UpdateRequest;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryDetail;
use App\Models\Event;
use App\Models\GoodIssue;
use App\Models\Kitchen;
use App\Models\ItemReturn;
use App\Models\InventoryDetailReturn;
use App\Models\ItemCategory;
use App\Models\Store;
use App\Models\User;
use App\Models\GoodReturn;
use App\Models\Item;
use App\Models\UomConversion;
use App\Models\PurchaseOrderDetail;
use Exception;

class IssuedToKitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = GoodIssue::query()
        ->with(['inventoryDetail.receivedBy', 'event', 'kitchen.place', 'createdBy'])
        ->where("event_id",'!=',null)
        ->get();
        // dd($results->toArray());
        return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $users = User::whereHas('roles', function ($query) {
          $query->whereIn('name', ['Store-Manager','Chef','Chef-Worker']);
        })->select('users.*')->with('roles')->get();

        $events = Event::all();
        $kitchens = Kitchen::all();
        $items = Item::all();
        return view($this->view, compact('events', 'kitchens','users', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddRequest $request)
    {
        $validated = $request->validated();
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
              $validated['return_by'] = $user->id;
            }
            $goodReturnId = GoodReturn::createWithTransaction([
              'event_id' => $validated['event_id'],
              'kitchen_id' => $validated['kitchen_id'],
              'return_by' => $validated['return_by'],
            ]);
            foreach ($validated['items'] as $key => $itemData) {
              $item = Item::with('itemBase')->find($itemData['item_id']);
              $itemBaseUnitId = $item->itemBase->unit_measure_id;
              $data = $this->dataToCreate(null, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
              $data['good_return_id'] = $goodReturnId->id;
              $data['inventory_detail_id'] = $itemData['inventory_detail_id'];
              $data['return_by'] = $validated['return_by'];
              $data['reason'] = $itemData['reason'];
              InventoryDetailReturn::createWithTransaction($data);
              updateItemDetails(
                $itemData['item_id'],
                [
                  'returned_quantity' => $data['quantity']
                ]
              );
            }
            DB::commit();
            return redirect()->route('goods-returned.index')->with('success', '' . $this->controller . ' returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to return ' . $this->controller . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $itemCategories = ItemCategory::all();
      $data = GoodIssue::query()
      ->with('event', 'kitchen')
      ->with('inventoryDetails', function ($query) {
        $query->with([
          'item.itemCategory', 'unitMeasureSelect', 'receivedBy', 'store.place'
        ]);
      })
      ->find($id);
      $singleInventoryDetail = $data->inventoryDetails->first();
      $result = [
        'id' => $data->id,
        'event_id' => $data->event->id,
        'event' => $data->event->name,
        'store' => $singleInventoryDetail->store->place->name . ' - ' . $singleInventoryDetail->store->floor,
        'kitchen' => $data->kitchen->floor_name,
        'received_by' => $singleInventoryDetail->receivedBy->name,
        'note' => $data->note,
        'items' => $data->inventoryDetails->map(function ($inventoryDetail) use ($data) {
          $purchaseOrderDetail = PurchaseOrderDetail::query()
          ->with('selectedUnitMeasure')
          ->where('item_id', $inventoryDetail->item->id)
          ->where('event_id', $data->event->id)
          ->first();
          // dd($purchaseOrderDetail,$inventoryDetail->item->id,$data->event->id);
          return [
            'id' => $inventoryDetail->item->id,
            'name' => $inventoryDetail->item->name,
            'category_id' => $inventoryDetail->item->itemCategory->id,
            'issued_uom' => $inventoryDetail->unitMeasureSelect->short_form,
            'issued_quantity' => $inventoryDetail->select_quantity,
            'requested_uom' => $purchaseOrderDetail->selectedUnitMeasure->short_form ?? '-',
            'requested_quantity' => $purchaseOrderDetail->select_quantity ?? '-',
            'inventory_detail_id' => $inventoryDetail->id
          ];
        })
        ->sortBy('inventory_detail_id')
        ->values()
      ];
      return view($this->view, compact('result', 'itemCategories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::all();
        $result = InventoryDetail::query()
        ->with(['item.itemBase.baseUom', 'receivedBy', 'event', 'kitchen', 'returns','return'])
        ->find($id);
        
        return view($this->view, compact('result','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $result = InventoryDetail::findOrFail($id);
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
            $validated['return_by'] = $user->id;
          }
            InventoryDetailReturn::createWithTransaction([
                'inventory_detail_id' => $result->id,
                'quantity' => $validated['returned_quantity'],
                'return_by' => $validated['return_by'],
                // 'reason' => $validated['reason'] ?? ''
            ]);
            updateItemDetails(
                $validated['item_id'],
                [
                  'returned_quantity' => $validated['returned_quantity']
                ]
            );
            return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' returned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to return ' . $this->controller . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function dataToCreate($vendorId = null, $itemData, $itemBaseUnitId, $unitMatched)
    {
      if ($unitMatched) {
      $data = [
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
          'select_unit_measure_id' => $itemData['unit_id'],
          'select_quantity' => $itemData['quantity'],
          'quantity' => $convertedQuantity,
      ];
      }
      if ($vendorId) {
        $data['vendor_id'] = $vendorId;
      }
      return $data;
    }
}
