<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GoodReturnedNote\AddRequest;
use App\Http\Requests\GoodReturnedNote\UpdateRequest;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryDetail;
use App\Models\Event;
use App\Models\Kitchen;
use App\Models\ItemReturn;
use App\Models\InventoryDetailReturn;
use App\Models\GoodReturn;
use App\Models\ItemCategory;

class GoodsReturnedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = GoodReturn::query()
        ->with('event', 'kitchen', 'returnBy')
        ->whereNotNull('event_id')
        ->whereNull('vendor_id')
        ->whereNull('purchase_order_id')
        ->get();
        return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddRequest $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $results = GoodReturn::query()
        ->with(['event', 'kitchen', 'returnBy', 'inventoryDetailReturns.inventoryDetail' => function ($query) {
            $query->with(['item', 'unitMeasure']);
        }])
        ->find($id);
        $items = $results->inventoryDetailReturns->map(function ($detailReturn) {
            return [
                'item_category_id' => $detailReturn->inventoryDetail->item->category_id,
                'item_name' => $detailReturn->inventoryDetail->item->name,
                'return_uom' => $detailReturn->inventoryDetail->unitMeasure->short_form,
                'returned_quantity' => $detailReturn->quantity,
                'created_at' => $detailReturn->created_at
            ];
        });
        $itemCategories = ItemCategory::all();
        return view($this->view, compact('results','itemCategories', 'items'));
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
}
