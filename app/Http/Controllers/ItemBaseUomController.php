<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemBaseUOM\AddRequest;
use App\Models\Item;
use App\Models\ItemBaseUom;
use App\Models\ItemCategory;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemBaseUomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $results = ItemBaseUom::with(['item', 'unitMeasure','baseUom', 'createdBy', 'updatedBy'])->get();
      // dd($results->toArray());
      return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $items = Item::get();
      $unitMeasure = UnitMeasure::get();
      
      return view($this->view,compact("items","unitMeasure"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddRequest $request)
    {
      $validated = $request->validated();
      try {
        $itemBaseUom = ItemBaseUom::createWithTransaction($validated);
        
        $itemBaseUom->unitMeasure()->sync($request->input("secondary_uom"));
        
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
        
        $items = Item::get();
        $unitMeasure = UnitMeasure::get();
        $result = ItemBaseUom::with(['item', 'unitMeasure','baseUom'])->findOrFail($id);
        // dd($result->toArray(),$this->view);
        return view($this->view, compact('result','items','unitMeasure'));
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddRequest $request, string $id)
    {
      $validated = $request->validated();

      try {
        $itemBaseUom =   ItemBaseUom::updateWithTransaction($id, $validated);
        $itemBaseUom->unitMeasure()->sync($request->input("secondary_uom"));
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
        ItemBaseUom::deleteWithTransaction($id);
       return redirect()->route($this->redirect)->with('success', ''.$this->controller.' deleted successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete '.$this->controller.': ' . $e->getMessage());
      }
    }
}
