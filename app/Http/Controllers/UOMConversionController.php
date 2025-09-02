<?php

namespace App\Http\Controllers;

use App\Http\Requests\UOMConversion\AddRequest;
use App\Models\UnitMeasure;
use App\Models\UomConversion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UOMConversionController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $uom_conversions = UomConversion::with('conversionUom', 'baseUom', 'createdBy', 'updatedBy')->get();
    return view($this->view, compact('uom_conversions'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $unitMeasures = UnitMeasure::get();
    return view($this->view, compact('unitMeasures'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {
    $validated = $request->validated();

    try {
      $validated['created_by'] = Auth::id();
      UomConversion::createWithTransaction($validated);
      return redirect()->route($this->redirect)
        ->with('success', ''.$this->controller.' created successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to create '.$this->controller.': ' . $e->getMessage());
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
      $unitMeasures = UnitMeasure::get();
      $result = UomConversion::findOrFail($id);
      return view($this->view, compact('result','unitMeasures'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, $id)
  {
    $validated = $request->validated();

    try {
      UomConversion::updateWithTransaction($id, $validated);
      return redirect()->route($this->redirect)
        ->with('success', ''.$this->controller.' updated successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to update '.$this->controller.': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    try {
      UomConversion::deleteWithTransaction($id);
      return redirect()->route($this->redirect)
        ->with('success', ''.$this->controller.' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to delete '.$this->controller.': ' . $e->getMessage());
    }
  }
}
