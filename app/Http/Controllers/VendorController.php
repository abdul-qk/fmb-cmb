<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendor\VendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Models\City;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\VendorBank;
use App\Models\VendorContactPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = Vendor::with('city.country','bank','contactPerson', 'createdBy', 'updatedBy')->get();
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $items = Item::all();
    $cities = City::get();
    return view($this->view, compact('cities', 'items'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(VendorRequest $request)
  {
    // dd($request->all());
    $validated = $request->validated();
    try {
      DB::beginTransaction();
      $vendorData = Arr::only($validated, ['name','city_id','address']);
      $vendorContact = Arr::only($validated, ['name','email','contact_number','office_number']);
      $vendorBank = Arr::only($validated, ['ntn','bank','account_no','bank_branch','bank_title','bank_address']);
      $items = Arr::only($validated, ['items']);
      $vendor = Vendor::createWithTransaction($vendorData);
      
      $vendorBank['vendor_id'] = $vendor->id;
      $vendorBank['primary'] = '1';
      
      $vendorContact['primary'] = '1';
      $vendorContact['vendor_id'] = $vendor->id;
      VendorContactPerson::createWithTransaction($vendorContact);
      
      if (!empty($validated['account_no'])) {
        VendorBank::createWithTransaction($vendorBank);
      }
      if (!empty($items['items']) && is_array($items['items'])) {
        $vendor->items()->sync($items['items']);
      }
      DB::commit();
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
    try {
      $result = Vendor::with([
        'banks',
        'contactPersons',
        'items.itemCategory',
        'city.country'
      ])
      ->findOrFail($id);

      // dd( $result->toArray());
      return view($this->view, compact('result'));
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
      $items = Item::all();
      $cities = City::get();
      $result = Vendor::find($id);
      $selectedItems = $result->items->pluck('id')->toArray(); 
      $vendorBank = VendorBank::where("vendor_id",$id)->where("primary",'1')->first();
      $vendorContact = VendorContactPerson::where("vendor_id",$id)->where("primary",'1')->first();
      return view($this->view, compact('result','items','cities', 'selectedItems','vendorBank','vendorContact'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find '.$this->controller.': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateVendorRequest $request, string $id)
  {
   
    $validated = $request->validated();
    try {
      DB::beginTransaction();

      $vendorData = Arr::only($validated, ['name','city_id','address']);
      $vendorContact = Arr::only($validated, ['name','email','contact_number','office_number']);
      $vendorBank = Arr::only($validated, ['ntn','bank','account_no','bank_branch','bank_title','bank_address']);
      $items = Arr::only($validated, ['items']);

      Vendor::updateWithTransaction($id, $vendorData);
      $vendor = Vendor::findOrFail($id);
      if(empty($validated['contact_id'])) {
        $vendorContact['primary'] = '1';
        $vendorContact['vendor_id'] = $vendor->id;
        VendorContactPerson::createWithTransaction($vendorContact);
      }else {
        VendorContactPerson::updateWithTransaction($validated['contact_id'],$vendorContact);
      }

      // if (!empty($validated['bank_id'])) {
      //   VendorBank::updateWithTransaction($validated['bank_id'],$vendorBank);
      // }
      if(empty($validated['bank_id'])  && !empty($validated['account_no'])) {
        $vendorBank['vendor_id'] = $vendor->id;
        $vendorBank['primary'] = '1';
        VendorBank::createWithTransaction($vendorBank);
      }else if (!empty($validated['bank_id'])) {
        VendorBank::updateWithTransaction($validated['bank_id'],$vendorBank);
      }
      
      if (!empty($items['items']) && is_array($items['items'])) {
        $vendor->items()->sync($items['items']);
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', $this->controller.' updated successfully.');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', 'Failed to update '.$this->controller.': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      $vendor = Vendor::findOrFail($id);
      Vendor::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success',  $this->controller.' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete  '.$this->controller.': ' . $e->getMessage());
    }
  }
}
