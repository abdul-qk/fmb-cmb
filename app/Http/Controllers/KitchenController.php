<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kitchen\AddRequest;
use App\Models\Event;
use App\Models\Location;
use App\Models\Place;
use App\Models\Kitchen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $results = Kitchen::with(['place', 'location', 'country', 'city', 'createdBy', 'updatedBy'])->get();
      return view($this->view, compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $places = Place::get();
      return view($this->view,compact('places'));
    }

    /**
     * Kitchen a newly created resource in storage.
     */
    public function store(AddRequest $request)
    {
      $validated = $request->validated();
      try {
        Kitchen::createWithTransaction($validated);
        return redirect()->route($this->redirect)->with('success', $this->controller.' created successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      try {
        $places = Place::get();
        $result = Kitchen::findOrFail($id);
        return view($this->view, compact('result','places'));
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
      }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      try {
        $places = Place::get();
        $result = Kitchen::findOrFail($id);
        return view($this->view, compact('result','places'));
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
        Kitchen::updateWithTransaction($id, $validated);
        return redirect()->route($this->redirect)->with('success', $this->controller.' updated successfully.');
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
        Kitchen::deleteWithTransaction($id);
       return redirect()->route($this->redirect)->with('success', $this->controller.' deleted successfully.');
      } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete '.$this->controller.': ' . $e->getMessage());
      }
    }
}
