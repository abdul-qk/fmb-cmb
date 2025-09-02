<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseOrder\AddOpenPurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\EditOpenPurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\ApproveOpenPurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\Currency;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\ItemBaseUom;
use App\Models\Place;
use App\Models\RecipeItem;
use App\Models\UnitMeasure;
use App\Models\UomConversion;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OpenPurchaseOrderController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = PurchaseOrder::with(['vendor', 'detail.item', 'detail.unitMeasure', 'currency', 'createdBy', 'updatedBy'])
    ->whereNotNull('vendor_id')
    ->where('type', 'po')
    ->doesntHave('events')
    ->get();
  
    $results->each(function ($result) {
      if (!empty($result->file_name)) {
        $parts = explode('_', $result->file_name);
        $folderName = array_slice($parts, 0, -2); 
        $newFilename = implode('_', $folderName);
        $segments = explode('/', $newFilename);

        // Now, split the original filename by '/'
        $operation = explode('/', $result->file_name);
        $filePath = 'po/' . $segments[1] . '/' . $operation[0] . '/' . $operation[1];
        $result->pdfUrl = Storage::url($filePath);
      } else {
        $result->pdfUrl = null;
      }
      if (!empty($result->approved_file_name)) {
        $parts = explode('_', $result->approved_file_name);
        $folderName = array_slice($parts, 0, -2);
        $newFilename = implode('_', $folderName);
        $segments = explode('/', $newFilename);

        $operation = explode('/', $result->approved_file_name);
        $filePath = 'po/' . $segments[1] . '/' . $operation[0] . '/' . $operation[1];
        $result->approvedPdfUrl = Storage::url($filePath);
      } else {
        $result->approvedPdfUrl = null;
      }
    });
    return view($this->view, compact('results'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $vendors = Vendor::all();
    $items = Item::all();
    $places = Place::all();
    $units = UnitMeasure::all();
    $currencies = Currency::all();
    return view($this->view, compact('vendors', 'items', 'units', "places", 'currencies'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddOpenPurchaseOrderRequest $request)
  {
    $random = rand(1000, 9999);
    $validated = $request->validated();

    $purchaseOrderData = Arr::only($validated, ['vendor_id', 'amount', 'discount', 'place_id', 'currency_id']);
    $purchaseOrderItemsData = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($validated['vendor_id']);
      $purchaseOrderData['file_name'] = 'create/' . $this->fileName($vendor, $random) . '.pdf';
      $purchaseOrderData['type'] = 'po';
      $purchaseOrder = PurchaseOrder::createWithTransaction($purchaseOrderData);
      $this->generatePDF($validated, $purchaseOrder, 'create', $vendor, $random);
      foreach ($purchaseOrderItemsData['items'] as $key => $purchaseOrderItemData) {
        // PurchaseOrderDetail::createWithTransaction([
        //   'purchase_order_id' => $purchaseOrder->id,
        //   'item_id' => $purchaseOrderItemData['item_id'],
        //   'unit_measure_id' => $purchaseOrderItemData['unit_id'],
        //   'quantity' => $purchaseOrderItemData['quantity'],
        //   'unit_price' => $purchaseOrderItemData['unit_price'],
        //   'total' => $purchaseOrderItemData['total'],
        // ]);
        $this->uomBase($purchaseOrder, $purchaseOrderItemData);
      }

      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' created successfully.');
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
    $places = Place::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'currency')->find($id);
    $currencies = Currency::all();
    $pdfUrl = '';
    $approvedPdfUrl = '';
    if ($result->file_name != "") {
      $parts = explode('_', $result->file_name);
      $folderName = array_slice($parts, 0, -2); 
      $newFilename = implode('_', $folderName);
      $segments = explode('/', $newFilename);

      // Now, split the original filename by '/'
      $operation = explode('/', $result->file_name);
      $filePath = 'po/' . $segments[1] . '/' . $operation[0] . '/' . $operation[1];
      $pdfUrl = Storage::url($filePath);
    }

    if ($result->approved_file_name != "") {
      $parts = explode('_', $result->approved_file_name);
      $folderName = array_slice($parts, 0, -2); 
      $newFilename = implode('_', $folderName);
      $segments = explode('/', $newFilename);

      // Now, split the original filename by '/'
      $operation = explode('/', $result->approved_file_name);
      $filePath = 'po/' . $segments[1] . '/' . $operation[0] . '/' . $operation[1];
      $approvedPdfUrl = Storage::url($filePath);
    }

    return view($this->view, compact('result', 'places', 'pdfUrl','approvedPdfUrl', 'currencies'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $places = Place::all();
    $vendors = Vendor::all();
    $items = Item::all();
    $units = UnitMeasure::all();
    $currencies = Currency::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure')->find($id);

    $pdfUrl = '';
    $approvedPdfUrl = '';
    if ($result->file_name != "") {
      $parts = explode('_', $result->file_name);
      $folderName = array_slice($parts, 0, -2); 
      $newFilename = implode('_', $folderName);
      $segments = explode('/', $newFilename);

      // Now, split the original filename by '/'
      $operation = explode('/', $result->file_name);
      $filePath = 'po/' . $segments[1] . '/' . $operation[0] . '/' . $operation[1];
      $pdfUrl = Storage::url($filePath);
    }

    return view($this->view, compact('result', 'items', 'units', 'vendors', 'places', 'pdfUrl', 'currencies'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(EditOpenPurchaseOrderRequest $request, string $id)
  {
    $random = rand(1000, 9999);
    $validated = $request->validated();
    $purchaseOrderData = Arr::only($validated, ['amount', 'discount', 'place_id', 'currency_id']);
    $purchaseOrderItemsData = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($request->current_vendor);
      $purchaseOrderData['file_name'] = 'edit/' . $this->fileName($vendor, $random) . '.pdf';

      $purchaseOrder = PurchaseOrder::updateWithTransaction($id, $purchaseOrderData);
      $this->generatePDF($validated, $purchaseOrder, "edit", $vendor, $random);

      $oldPurchaseOrderDetailIds = PurchaseOrderDetail::where('purchase_order_id', $id)->pluck('id');
      foreach ($oldPurchaseOrderDetailIds as $oldPurchaseOrderDetailId) {
        PurchaseOrderDetail::deleteWithTransaction($oldPurchaseOrderDetailId);
      }
      foreach ($purchaseOrderItemsData['items'] as $key => $purchaseOrderItemData) {
        // PurchaseOrderDetail::createWithTransaction([
        //   'purchase_order_id' => $purchaseOrder->id,
        //   'item_id' => $purchaseOrderItemData['item_id'],
        //   'unit_measure_id' => $purchaseOrderItemData['unit_id'],
        //   'quantity' => $purchaseOrderItemData['quantity'],
        //   'unit_price' => $purchaseOrderItemData['unit_price'],
        //   'total' => $purchaseOrderItemData['total'],
        // ]);
        $this->uomBase($purchaseOrder, $purchaseOrderItemData);

      }

      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
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
      PurchaseOrder::deleteWithTransaction($id);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function approveOpenPurchaseOrder(string $id)
  {
    $places = Place::all();
    $currencies = Currency::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure')->find($id);

    return view($this->view, compact('result', 'places', 'currencies'));
  }

  // public function storeApproveOpenPurchaseOrder(ApproveOpenPurchaseOrderRequest $request, string $id)
  public function storeApproveOpenPurchaseOrder(ApproveOpenPurchaseOrderRequest $request, string $id)
  {
    $random = rand(1000, 9999);
    $validated = $request->validated();
    $approvedPurchaseOrderDetails = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($request->current_vendor);

      $purchaseOrder =  PurchaseOrder::updateWithTransaction($id, [
        'status' => 'approved',
        'approved_by' => Auth::id(),
        'approved_file_name' => 'approved/' . $this->fileName($vendor, $random) . '.pdf'
      ]);

      $this->generatePDF($validated, $purchaseOrder, "approved", $vendor, $random);

      foreach ($approvedPurchaseOrderDetails['items'] as $key => $approvedPurchaseOrderDetail) {
        ApprovedPurchaseOrderDetail::createWithTransaction([
          'purchase_order_detail_id' => $approvedPurchaseOrderDetail['purchase_order_detail_id'],
          'quantity' => $approvedPurchaseOrderDetail['quantity'],
          'total' => $approvedPurchaseOrderDetail['total'],
        ]);
      }
      DB::commit();
      return redirect()->route($this->redirect)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to update ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function rejectOpenPurchaseOrder(string $id)
  {
    try {
      PurchaseOrder::updateWithTransaction($id, ['status' => 'rejected', 'rejected_by' => Auth::id()]);
      return redirect()->route($this->redirect)->with('success', $this->controller . ' rejected successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to reject ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  public function generatePDF($record, $purchase, $created, $vendor, $random_number)
  {
    $place = Place::with('location.country.city')->find($record['place_id']);
    $currency = Currency::find($record['currency_id']);
    $user = User::find($purchase['created_by']);

    $itemInfos = [];
    foreach ($record['items'] as $key => $purchaseOrderItemData) {
      $unitMeasure = UnitMeasure::find($purchaseOrderItemData['unit_id']);
      $item = Item::find($purchaseOrderItemData['item_id']);
      $itemInfos[] = [
        'item' => $item->name ?? '-',
        'unit_measure' => $unitMeasure->name ?? '-',
        'quantity' => $purchaseOrderItemData['quantity'],
        'unit_price' => $purchaseOrderItemData['unit_price'],
        'total' => $purchaseOrderItemData['total'],
      ];
    }

    $data = [
      'vendorDetail' => $vendor->toArray(),
      'place' => $place->toArray() ?? '-',
      'currency' => $currency->toArray() ?? '-',
      'totalAmount' => $record['amount'],
      'itemInfos' => $itemInfos,
      'purchaseDetail' => $purchase->toArray(),
      'userName' => $user->name ?? '-',
    ];

    $view = view('pdf.purchase-order', ['data' => $data])->render();

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($view);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = $this->fileName($vendor, $random_number);

    $pdfFolder = storage_path('app/public/po/' . $this->fileName($vendor) . '/' . $created);
    $pdfFileName =  '/' . $fileName . '.pdf';
    $pdfFilePath = $pdfFolder . '' . $pdfFileName;

    if (!file_exists($pdfFolder)) {
      mkdir($pdfFolder, 0777, true);
    }
    file_put_contents($pdfFilePath, $dompdf->output());
    return $pdfFilePath;
  }

  public function fileName($data, $random_number = null)
  {
    $vendor_name = ucwords(Str::of($data->name)->lower());
    $vendor_name = str_replace(' ', '', $vendor_name);
    $currentDate = date('Y-m-d');

    // Construct the filename based on whether the random number is provided
    if (!is_null($random_number) && $random_number !== '') {
      return 'PO_' . $data->id . '_' . $vendor_name . '_' . $currentDate . '_' . $random_number;
    }

    return 'PO_' . $data->id . '_' . $vendor_name;
  }

  public function uomBase($purchaseOrder, $purchaseOrderItemData)
  {
    // Get input data
    $itemId = $purchaseOrderItemData['item_id'];
    $selectedMeasure = $purchaseOrderItemData['unit_id'];
    $itemQuantity = $purchaseOrderItemData['quantity'];

    $itemBaseUom = ItemBaseUom::with("baseUom")->where("item_id", $itemId)->first();
    if (!$itemBaseUom) {
      return response()->json(['success' => false, 'message' => 'Base UOM not found for the given item.']);
    }
    $baseUOM = $itemBaseUom->unit_measure_id;

    if ($baseUOM == $selectedMeasure) {
      $this->createOrUpdateRecipeItem($purchaseOrder, $purchaseOrderItemData, $baseUOM, $itemQuantity, $selectedMeasure);
      return;
    }

    // Get UOM conversion
    $check = 1;
    $checking = UomConversion::where('base_uom', $baseUOM)
      ->where('secondary_uom', $selectedMeasure)
      ->first();
    if (!$checking) {
      $checking = UomConversion::where('base_uom', $selectedMeasure)
        ->where('secondary_uom', $baseUOM)
        ->first();
      $check = 2;
    }

    // Create or update the recipe item
    $convertedQuantity = $check == 1
      ? (float)(1 / $checking->conversion_value) * (float)$itemQuantity
      : (float)$checking->conversion_value * (float)$itemQuantity;

    $this->createOrUpdateRecipeItem($purchaseOrder, $purchaseOrderItemData, $baseUOM, $convertedQuantity, $selectedMeasure);
  }

  private function createOrUpdateRecipeItem($purchaseOrder, $purchaseOrderItemData, $baseUOM, $itemQuantity, $selectedMeasure)
  {
    $data = [
      'purchase_order_id' => $purchaseOrder->id,
      'item_id' => $purchaseOrderItemData['item_id'],
      'select_quantity' => $purchaseOrderItemData['quantity'],
      'select_unit_measure_id' => $selectedMeasure,
      'unit_measure_id' => $baseUOM,
      'quantity' => $itemQuantity,
      'unit_price' => $purchaseOrderItemData['unit_price'],
      'total' => $purchaseOrderItemData['total'],
    ];
    PurchaseOrderDetail::createWithTransaction($data);
  }
}
