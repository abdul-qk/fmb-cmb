<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GoodReturnedNote\AddRequest;
use App\Http\Requests\Inventory\AdjustmentRequest;
use App\Http\Requests\GoodReturnedNote\UpdateRequest;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryDetail;
use App\Models\Event;
use App\Models\Kitchen;
use App\Models\ItemReturn;
use App\Models\InventoryDetailReturn;
use App\Models\SupplierReturn;
use App\Models\Item;
use App\Models\UomConversion;
use App\Models\GoodReturn;
use App\Models\ItemCategory;
use Exception;
use Illuminate\Support\Facades\Auth;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = GoodReturn::query()
        ->with(['returnBy'])
        ->whereNull('vendor_id')
        ->whereNull('event_id')
        ->whereNull('purchase_order_id')
        ->get();
        return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::withWhereHas('detail')
        ->get()
        ->filter(function ($item) {
            return $item->detail->available_quantity > 0;
        })
        ->values();
        return view($this->view, compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdjustmentRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $goodReturn = GoodReturn::createWithTransaction([
                'return_by' => Auth::id()
            ]);
            foreach ($validated['items'] as $key => $itemData) {
                $itemBaseUnitId = $itemData['base_unit_id'];
                $data = $this->dataToCreate(null, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
                $data['reason'] = $itemData['reason'];
                if ($data['quantity'] > $itemData['available_quantity']) {
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Converted return quantity ' . $data['quantity'] . ' cannot be greater than available quantity ' . $itemData['available_quantity']);
                }
                $data['good_return_id'] = $goodReturn->id;
                SupplierReturn::createWithTransaction($data);
                updateItemDetails(
                $itemData['item_id'],
                [
                    'adjusted_quantity' => $data['quantity']
                ]
                );
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
        $results = GoodReturn::query()
        ->with(['returnBy', 'supplierReturn' => function ($query) {
            $query->with(['item' => function ($query) {
                $query->with('detail');
            }, 'unitMeasure']);
        }])
        ->find($id);
        $itemCategories = ItemCategory::all();
        return view($this->view, compact('results','itemCategories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      
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
}
