<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SupplierReturn\AddRequest;
use App\Http\Requests\Inventory\SupplierReturnRequest;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\SupplierReturn;
use App\Models\UomConversion;
use App\Models\GoodReturn;
use App\Models\ItemCategory;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class SupplierReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $results = SupplierReturn::with('createdBy','vendor','item','unitMeasure')
        // ->whereHas("vendor")
        // ->get();
        $results = GoodReturn::query()
        ->with(['vendor', 'returnBy'])
        ->whereNotNull('purchase_order_id')
        ->get();
        return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::all();
        $grns = PurchaseOrder::query()
        ->whereNotNull('vendor_id')
        ->where('type', 'grn')
        ->doesntHave('events')
        ->get();
        return view($this->view, compact('grns', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(AddRequest $request)
    // {
    //     $validated = $request->validated();
    //     try {
    //         DB::beginTransaction();
    //         $vendorId = $validated['vendor_id'];
    //         foreach ($validated['items'] as $key => $itemData) {
    //             $item = Item::with('itemBase')->find($itemData['item_id']);
    //             $itemBaseUnitId = $item->itemBase->unit_measure_id;
    //             $data = $this->dataToCreate($vendorId, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
    //             SupplierReturn::createWithTransaction($data);
    //             updateItemDetails(
    //                 $itemData['item_id'],
    //                 [
    //                   'supplier_returned_quantity' => $data['quantity']
    //                 ]
    //             );
    //         }
    //         DB::commit();
    //         return redirect()->route('inventories.index')->with('success', $this->controller.' created successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with('error', 'Failed to create '.$this->controller.': ' . $e->getMessage());
    //     }
    // }

    public function store(SupplierReturnRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $vendorId = $validated['vendor_id'];
            $goodReturn = GoodReturn::createWithTransaction([
                'vendor_id' => $vendorId,
                'return_by' => Auth::id(),
                'purchase_order_id' => $validated['grn_id']
            ]);
            foreach ($validated['items'] as $key => $itemData) {
                $item = Item::with('itemBase')->find($itemData['item_id']);
                $itemBaseUnitId = $item->itemBase->unit_measure_id;
                $data = $this->dataToCreate($vendorId, $itemData, $itemBaseUnitId, ($itemBaseUnitId == $itemData['unit_id'] ? true : false));
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
                    'supplier_returned_quantity' => $data['quantity']
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
        ->with(['vendor', 'returnBy', 'supplierReturn' => function ($query) {
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // private function dataToCreate($vendorId, $itemData, $itemBaseUnitId, $unitMatched)
    // {
    //     if ($unitMatched) {
    //         $data = [
    //             'vendor_id' => $vendorId,
    //             'item_id' => $itemData['item_id'],
    //             'unit_measure_id' => $itemData['unit_id'],
    //             'quantity' => $itemData['quantity'],
    //         ];
    //     } else {
    //         $check = 1;
    //         $checking = UomConversion::where('base_uom', $itemBaseUnitId)
    //         ->where('secondary_uom', $itemData['unit_id'])
    //         ->first();
    //         if (!$checking) {
    //             $checking = UomConversion::where('base_uom', $itemData['unit_id'])
    //             ->where('secondary_uom', $itemBaseUnitId)
    //             ->first();
    //             $check = 2;
    //         }
    //         $convertedQuantity = $check == 1
    //         ? (float)(1 / $checking->conversion_value) * (float)$itemData['quantity']
    //         : (float)$checking->conversion_value * (float)$itemData['quantity'];
    //         $data = [
    //             'vendor_id' => $vendorId,
    //             'item_id' => $itemData['item_id'],
    //             'select_unit_measure_id' => $itemData['unit_id'],
    //             'unit_measure_id' => $itemBaseUnitId,
    //             'select_quantity' => $itemData['quantity'],
    //             'quantity' => $convertedQuantity,
    //         ];
    //     }
    //     return $data;
    // }

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
