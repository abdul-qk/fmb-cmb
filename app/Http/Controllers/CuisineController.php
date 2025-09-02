<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DishCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DishCategory\AddRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CuisineController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $dishCategories = DishCategory::with(['createdBy','updatedBy'])->get();
    return view($this->view, compact('dishCategories'));
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
    try {
      $validated = $request->validated();
      DishCategory::createWithTransaction($validated);
      return redirect()->route($this->redirect)->with('success',  $this->controller.' created successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
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
      $dishCategory = DishCategory::findOrFail($id);
      return view($this->view, compact('dishCategory'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, string $id)
  {
    try {
      $validated = $request->validated();
      $validated["updated_by"] = Auth::id();
      DishCategory::updateWithTransaction($id, $validated);
      return redirect()->route($this->redirect)->with('success', $this->controller.' updated successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      DishCategory::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success', $this->controller.' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }
}
