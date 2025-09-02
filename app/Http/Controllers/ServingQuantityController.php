<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServingQuantity\AddRequest;
use App\Http\Requests\ServingQuantity\UpdateRequest;
use App\Models\Event;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Place;
use App\Models\ServingItem;
use App\Models\ServingQuantity;
use App\Models\ServingQuantityTiffin;
use App\Models\TiffinSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ServingQuantityController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = ServingQuantity::with('servingQuantityItems.servingQuantityTiffinItems', 'createdBy', 'updatedBy')->get();

    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $results = TiffinSize::get();
    $places = Place::get();
    return view($this->view, compact('results', 'places'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  {
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      if ($validated["serving"] == "Thaal") {
        $validated['serving_person'] = $validated['quantity'] * 8;
        ServingQuantity::createWithTransaction($validated);
      } else {
        $totalValue = 0;
        foreach ($validated['items'] as $item) {
          $totalValue += $item['person_no'] * $item['quantity'];
        }

        $servingQuantity = ServingQuantity::createWithTransaction([
          'serving' => $validated['serving'],
          'serving_person' => $totalValue,
        ]);
        foreach ($validated['items'] as $item) {
          ServingQuantityTiffin::create([
            'serving_quantity_id' => $servingQuantity->id,  // Link to main ServingQuantity entry
            'tiffin_size_id' => $item['tiffin_size_id'],
            'quantity' => $item['quantity'],
            'date_from' => $item['date_from'],
            'date_to' => $item['date_to'],
          ]);
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      // $result = Event::with(['servingItem','servingItem.getTiffinSize'])->findOrFail($id);
      // dd($result->toArray());
      $result = ServingQuantity::with('servingQuantityItems.servingQuantityTiffinItems', 'createdBy', 'updatedBy')->findOrFail($id);

      $servingItems = ServingItem::where('event_id', $result->id)->get();
      return view($this->view, compact('result', 'tiffinSizes', 'servingItems', 'places'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {

    try {
      $places = Place::get();

      $tiffinSizes = TiffinSize::get();
      // $result = Event::with(['servingItem','servingItem.getTiffinSize'])->findOrFail($id);
      // dd($result->toArray());
      $result = ServingQuantity::with('servingQuantityItems.servingQuantityTiffinItems', 'createdBy', 'updatedBy')->findOrFail($id);

      $servingItems = ServingItem::where('event_id', $result->id)->get();
      return view($this->view, compact('result', 'tiffinSizes', 'servingItems', 'places'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateRequest $request, string $id)
  // public function update(Request $request, string $id)
  {

    $validated = $request->validated();
    try {
      DB::beginTransaction();
      if ($validated["serving"] == "Thaal") {
        $validated['serving_person'] = $validated['quantity'] * 8;
        ServingQuantity::updateWithTransaction($id, $validated);
      } else {
        $totalValue = 0;
        foreach ($validated['items'] as $item) {
          $totalValue += $item['person_no'] * $item['quantity'];
        }
        ServingQuantity::updateOrCreate(
          [
            'id' => $id,
          ],
          [
            'serving_person' => $totalValue,
          ]
        );

        foreach ($validated['items'] as $item) {
          ServingQuantityTiffin::updateOrCreate(
            [
              'id' => $item['id'],
              'serving_quantity_id' => $id,
              'tiffin_size_id' => $item['tiffin_size_id'],
            ],
            [
              'quantity' => $item['quantity'],
              'date_from' => $item['date_from'],
              'date_to' => $item['date_to'],
            ]
          );
        }
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      ServingQuantity::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete ' . $this->controller . ': ' . $e->getMessage());
    }
  }
}
