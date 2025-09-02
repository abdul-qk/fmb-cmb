<?php

namespace App\Http\Controllers;

use App\Http\Requests\Item\AddRequest;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\ItemBaseUom;
use App\Models\ItemCategory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Store;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $results = Item::with(['itemCategory', 'createdBy', 'updatedBy','itemBase.baseUom','itemBase.unitMeasure'])->get();
      // dd($results->toArray());
      return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $itemsCategories = ItemCategory::get();
      $unitMeasure = UnitMeasure::get();
      $stores = Store::get();
      return view($this->view,compact("itemsCategories","unitMeasure","stores"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddRequest $request)
    {
      // dd($request->all());
      $validated = $request->validated();
      $validated_1 = Arr::except($validated, ['unit_measure_id','secondary_uom','quantity','store_id']);
      $validated_2 = Arr::only($validated, ['unit_measure_id','secondary_uom']);
     
      try {
        $item = Item::createWithTransaction($validated_1);
        $validated_2["item_id"] = $item->id;
        $itemBaseUom = ItemBaseUom::createWithTransaction($validated_2);
        
        $itemBaseUom->unitMeasure()->sync($request->input("secondary_uom"));

        if($request->quantity && $request->store_id) {
          
          $purchaseOrder = PurchaseOrder::createWithTransaction([
            "place_id" => Auth::user()->place_id,
            "status" => "approved",
            "approved_by" => Auth::id(),
            'type' => 'default',
            'currency_id' => 2
          ]);

          $approvedPurchaseOrderDetail = PurchaseOrderDetail::createWithTransaction([
            'purchase_order_id' => $purchaseOrder->id,
            'item_id' => $item->id,
            'unit_measure_id' => $request->unit_measure_id,
            'select_unit_measure_id' => $request->unit_measure_id,
            'select_quantity' => $request->quantity ?? 0,
            'quantity' => $request->quantity ?? 0,
            'unit_price' => $request->per_unit_price ?? 0,
          ]);
          $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::createWithTransaction([
            'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
            'quantity' => $request->quantity ?? 0,
          ]);
          Inventory::createWithTransaction([
            'approved_purchase_order_detail_id' => $approved_purchase_order_detail->id,
            'quantity' => $request->quantity ?? 0,
            'remaining' => 0,
            'inventory_status' => 'Completed',
            'store_id' => $request->store_id,
          ]);

          updateItemDetails(
            $item->id,
            [
              'received_quantity' => $request->quantity ?? 0
            ]
          );
        }

        return redirect()->route($this->redirect)->with('success', ''.$this->controller.' created successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      try {
        $itemsCategories = ItemCategory::get();
        $result = Item::findOrFail($id);

        $unitMeasure = UnitMeasure::get();
        $itemBaseUom = ItemBaseUom::with(['item', 'unitMeasure','baseUom'])->where('item_id',$id)->first();

        return view($this->view, compact('result','itemsCategories','unitMeasure','itemBaseUom'));
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddRequest $request, string $id)
    {
      // dd($request['base_uom_id']);
      $validated = $request->validated();
      $validated_1 = Arr::except($validated, ['unit_measure_id','secondary_uom']);
      $validated_2 = Arr::only($validated, ['unit_measure_id','secondary_uom']);
      try {
        $item = Item::updateWithTransaction($id, $validated_1);

        if ($request['base_uom_id']) {
          $itemBaseUom =   ItemBaseUom::updateWithTransaction($request['base_uom_id'], $validated_2);
          $itemBaseUom->unitMeasure()->sync($request->input("secondary_uom"));
        }else {
          
          $validated_2["item_id"] = $item->id;
          $itemBaseUom = ItemBaseUom::createWithTransaction($validated_2);
          
          $itemBaseUom->unitMeasure()->sync($request->input("secondary_uom"));
        }

        return redirect()->route($this->redirect)->with('success', ''.$this->controller.' updated successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      try {
        Item::deleteWithTransaction($id);
       return redirect()->route($this->redirect)->with('success', ''.$this->controller.' deleted successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete '.$this->controller.': ' . $e->getMessage());
      }
    }
}
