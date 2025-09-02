<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dish\AddRequest as DishAddRequest;
use Illuminate\Http\Request;
use App\Models\DishCategory;
use App\Models\Dish;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $dishes = Dish::with(['createdBy','updatedBy','dishCategory'] )->get();
      // dd($dishes->toArray());
      return view($this->view, compact('dishes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $dishCategories = DishCategory::all();
      return view($this->view, compact('dishCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DishAddRequest $request)
    {
        $validated = $request->validated();
        try {
          Dish::createWithTransaction($validated);
            
          return redirect()->route($this->redirect)->with('success', 'Dish created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Dish: ' . $e->getMessage());
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
        $dish = Dish::findOrFail($id);
        $categories = DishCategory::all();
        return view($this->view, compact('dish','categories'));
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find Dish: ' . $e->getMessage());
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DishAddRequest $request, string $id)
    {
      $validated = $request->validated();

      try {
        Dish::updateWithTransaction($id, $validated);
        return redirect()->route($this->redirect)->with('success', 'Dish updated successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update Dish: ' . $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      try {
        Dish::deleteWithTransaction($id);
        return redirect()->route($this->redirect)->with('success', 'Dish deleted successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete Dish: ' . $e->getMessage());
      }
    }
}
