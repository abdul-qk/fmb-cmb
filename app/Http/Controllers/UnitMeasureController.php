<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitMeasure\AddRequest;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitMeasureController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $unitMeasures = UnitMeasure::with(['createdBy', 'updatedBy'])->get();
    // dd($unitMeasures);
    return view($this->view, compact('unitMeasures'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view($this->view);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {
    $validated = $request->validated();

    try {
      $validated['created_by'] = Auth::id();
      UnitMeasure::createWithTransaction($validated);
      return redirect()->route($this->redirect)
        ->with('success', 'Measurement created successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to create Measurement: ' . $e->getMessage());
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
      $result = UnitMeasure::findOrFail($id);
      return view($this->view, compact('result'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find Unit Measure: ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request,$id)
  {
    $validated = $request->validated();

    try {
      UnitMeasure::updateWithTransaction($id, $validated);
      return redirect()->route($this->redirect)
        ->with('success', 'Measurement updated successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to update Measurement: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    try {
      UnitMeasure::deleteWithTransaction($id);
      return redirect()->route($this->redirect)
        ->with('success', 'Measurement deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to delete Measurement: ' . $e->getMessage());
    }
  }
}
