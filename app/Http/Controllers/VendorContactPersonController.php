<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorContactPerson\ContactPersonRequest;
use App\Models\VendorContactPerson;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class VendorContactPersonController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index()
    {
        $results = VendorContactPerson::with(['vendor', 'createdBy', 'updatedBy'])->get();
        return view($this->view, compact('results'));
    }

    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        $vendors = Vendor::all();
        return view($this->view, compact('vendors'));
    }

    /**
    * Store a newly created resource in storage.
    */
    public function store(ContactPersonRequest $request)
    {
        $validated = $request->validated();
        try {
          if (isset($validated['primary']) && $validated['primary']) {
            VendorContactPerson::where('vendor_id', $validated['vendor_id'])->update(['primary' => "0"]);
          }
            VendorContactPerson::createWithTransaction($validated);
            return redirect()->route($this->redirect)->with('success', ''.$this->controller.' created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        $vendors = Vendor::all();
        $result = VendorContactPerson::find($id);
        return view($this->view, compact('result','vendors'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ContactPersonRequest $request, string $id)
  {
    $validated = $request->validated();
    try {
      if (isset($validated['primary']) && $validated['primary']) {
        VendorContactPerson::where('vendor_id', $validated['vendor_id'])->update(['primary' => "0"]);
      }
      VendorContactPerson::updateWithTransaction($id, $validated);
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
      $result = VendorContactPerson::findOrFail($id);
      if($result->primary == "1") {
        return redirect()->back()->with('error', 'This is Primary Contact.');  
      }
      VendorContactPerson::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success',  $this->controller.' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete  '.$this->controller.': ' . $e->getMessage());
    }
  }
}
