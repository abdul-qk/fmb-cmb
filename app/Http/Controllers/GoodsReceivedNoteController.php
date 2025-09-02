<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GoodReceivedNote\AddRequest;
use App\Http\Requests\GoodReceivedNote\EditRequest;
use App\Http\Requests\GoodReceivedNote\ApproveRequest;
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
use App\Models\Inventory;
use App\Models\ItemCategory;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Store;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class GoodsReceivedNoteController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {

    $vendorId = request('stitching_type');
    $date_from = request('date_from');
    $date_to   = request('date_to');
    
    $results = PurchaseOrder::with(['vendor.contactPerson', 'detail.item', 'detail.unitMeasure', 'currency', 'createdBy', 'updatedBy'])
      ->whereNotNull('vendor_id')
      ->where('type', 'grn')
      ->doesntHave('events')
      ->when($vendorId, fn ($query) => $query->where('vendor_id', $vendorId))
      ->when($date_from && !$date_to, fn ($query) => $query->whereDate('grn_date', '>=', $date_from))
      ->when(!$date_from && $date_to, fn ($query) => $query->whereDate('grn_date', '<=', $date_to))
      ->when($date_from && $date_to, fn ($query) => $query->whereBetween('grn_date', [$date_from, $date_to]))
      ->get();

    $vendors = Vendor::get();

    $results->each(function ($result) {
      if (!empty($result->file_path)) {
        $parts = explode('_', $result->file_path);
        $folderName = array_slice($parts, 0, -2);
        $newFilename = implode('_', $folderName);
        $segments = explode('/', $newFilename);

        // Now, split the original filename by '/'
        $operation = explode('/', $result->file_path);
        $filePath =  $operation[0] . '/' . $operation[1] . '/' . $operation[2] . '/' . $operation[3];

        $filePaths = Storage::disk('public')->files('uploads/upload-bill/' . $operation[3]);

        $imageData = array_map(function ($file) {
          return [
            'url' => Storage::url($file),
            'type' => pathinfo($file, PATHINFO_EXTENSION),
          ];
        }, $filePaths);
        $result->imageData = $imageData;
        // dd($result->imageData);
      } else {
        $result->imageData = null;
      };
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
    return view($this->view, compact('results', 'vendors','date_to','date_from'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $vendors = Vendor::all();
    $items = Item::all();
    $stores = Store::all();
    $units = UnitMeasure::all();
    $currencies = Currency::all();
    $customers = User::whereHas('roles', function ($query) {
      $query->whereIn('name', ['Store-Manager', 'Accountant']);
    })->select('users.*')->with('roles')->get();


    $fixedCurrencyEditable = false;
    $fixedDate = false;

    $hasAccessGrnDateEditable = $this->accessPermission("grn-date-editable");
    $hasAccessCurrencyFixed = $this->accessPermission("default-currency");
    
    $hasAccessStoreFixed = $this->accessPermission("default-store");

    // dd($hasAccessGrnDateEditable,$hasAccessCurrencyFixed,$hasAccessStoreFixed);

    return view($this->view, compact('vendors', 'items', 'units', "stores", 'currencies', 'hasAccessCurrencyFixed', 'hasAccessGrnDateEditable','hasAccessStoreFixed', 'customers'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddRequest $request)
  // public function store(Request $request)
  {
    // dd($request->all());
    $random = rand(1000, 9999);
    $validated = $request->validated();


    // $purchaseOrderData = Arr::only($validated, ['vendor_id', 'grn_date', 'paid_by', 'sub_amount','bill_no', 'additional_charges', 'amount', 'discount', 'store_id', 'currency_id']);
    $purchaseOrderData = Arr::only($validated, ['vendor_id', 'grn_date', 'paid_by', 'sub_amount', 'bill_no', 'additional_charges', 'amount', 'discount', 'store_id', 'currency_id', 'description']);

    $documentsPath = 'public/uploads/upload-bill/' . $this->formatUserName($validated) . '/';

    $store =  Store::find($validated['store_id']);
    $this->handleFileUploads($request, $documentsPath, 'upload_bill');

    $purchaseOrderData['file_path'] = $documentsPath;
    $purchaseOrderData['place_id'] = $store->place_id;
    $purchaseOrderItemsData = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($validated['vendor_id']);
      $purchaseOrderData['type'] = 'grn';
      $purchaseOrder = PurchaseOrder::createWithTransaction($purchaseOrderData);
      foreach ($purchaseOrderItemsData['items'] as $key => $purchaseOrderItemData) {
        $purchaseOrderDetails = $this->uomBase($purchaseOrder, $purchaseOrderItemData);
        $approvedDetail['items'][$key]['purchase_order_detail_id'] = $purchaseOrderDetails['id'];
        $approvedDetail['items'][$key]['item_id'] = $purchaseOrderDetails['item_id'];
        $approvedDetail['items'][$key]['unit_price'] = $purchaseOrderDetails['unit_price'];
        $approvedDetail['items'][$key]['unit_id'] = $purchaseOrderDetails['unit_measure_id'];
        $approvedDetail['items'][$key]['quantity'] = $purchaseOrderDetails['quantity'];
        $approvedDetail['items'][$key]['total'] = $purchaseOrderDetails['total'];
        if (isset($purchaseOrderDetails['sub_total'])) {
          $approvedDetail['items'][$key]['sub_total'] = $purchaseOrderDetails['sub_total'];
          $approvedDetail['items'][$key]['per_item_discount'] = $purchaseOrderDetails['per_item_discount'] ?? 0;
          $approvedDetail['items'][$key]['discount_option'] = $purchaseOrderDetails['discount_option'];
        }
      }
      $approvedDetail['current_vendor'] = $purchaseOrderData['vendor_id'];
      $approvedDetail['amount'] = $purchaseOrderData['amount'];
      $approvedDetail['place_id'] = $store->place_id;
      $approvedDetail['currency_id'] = $purchaseOrderData['currency_id'];
      $approvedDetail['discount'] = $purchaseOrderData['discount'];
      $approvedDetail['store_id'] = $validated['store_id'];
      $this->storeNewApproveOpenPurchaseOrder($approvedDetail, $purchaseOrder->id);

      DB::commit();
      return redirect()->route($this->redirect)->withInput()->with('success', '' . $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->withInput()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $stores = Store::all();
    $places = Place::all();
    $itemCategories = ItemCategory::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'currency')->find($id);
    $customers = User::whereHas('roles', function ($query) {
      $query->whereIn('name', ['CUSTOMER']);
    })->select('users.*')->with('roles')->get();
    $currencies = Currency::all();
    $pdfUrl = '';
    $approvedPdfUrl = '';

    if (!empty($result->file_path)) {
      $parts = explode('_', $result->file_path);
      $folderName = array_slice($parts, 0, -2);
      $newFilename = implode('_', $folderName);
      $segments = explode('/', $newFilename);

      // Now, split the original filename by '/'
      $operation = explode('/', $result->file_path);
      $filePath =  $operation[0] . '/' . $operation[1] . '/' . $operation[2] . '/' . $operation[3];

      $filePaths = Storage::disk('public')->files('uploads/upload-bill/' . $operation[3]);
      
      $imageData = array_map(function ($file) use ($operation) {
        return [
            'url' => Storage::url($file),
            'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            'image' => basename($file),
            'user_id' => $operation[3] ,
            // 'user_id' => explode('_', $operation[3])[1] ,
        ];
      }, $filePaths);
      $result->imageData = $imageData;
      // dd($result->imageData);
    }

    // dd($result->imageData);

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

    return view($this->view, compact('result', 'itemCategories', 'places', 'pdfUrl', 'approvedPdfUrl', 'currencies', 'stores', 'customers'));
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
  public function update(EditRequest $request, string $id)
  {
    $random = rand(1000, 9999);
    $validated = $request->validated();
    $purchaseOrderData = Arr::only($validated, ['amount', 'discount', 'place_id', 'currency_id']);
    $purchaseOrderItemsData = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($request->current_vendor);

      $purchaseOrder = PurchaseOrder::updateWithTransaction($id, $purchaseOrderData);

      $oldPurchaseOrderDetailIds = PurchaseOrderDetail::where('purchase_order_id', $id)->pluck('id');
      foreach ($oldPurchaseOrderDetailIds as $oldPurchaseOrderDetailId) {
        PurchaseOrderDetail::deleteWithTransaction($oldPurchaseOrderDetailId);
      }
      foreach ($purchaseOrderItemsData['items'] as $key => $purchaseOrderItemData) {
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

  public function datatableListing(Request $request)
  {
    // $offset = $request->input('start', 0); // Datatable uses "start"
    // $limit = $request->input('length', 10); // Datatable uses "length"
    // $orderBy = $request->input('order.0.column', 'id'); // Default column index
    // $orderDir = $request->input('order.0.dir', 'asc'); // Sorting direction

    $offset = request()->query('offset');
    $limit = request()->query('limit');
    $orderBy = request()->query('orderBy');
    $orderType = request()->query('orderType');

    $date_from = $request->input('date_from');
    $date_to = $request->input('date_to');
    $vendor_id = $request->input('vendor_id');

    $textSearch = request()->query('textSearch');
    $textSearchValues = explode(' ', $textSearch);

    $textSearchColumns = ['vendors.contact_persons.contact_number', 'vendors.name', 'purchase_orders.id', 'purchase_orders.grn_date', 'purchase_orders.discount', 'purchase_orders.amount', 'purchase_orders.bill_no'];
    // dd($status,$balance);

    // Start query builder
    $query = PurchaseOrder::with(['vendor.contactPerson', 'detail.item', 'detail.unitMeasure', 'currency', 'createdBy', 'updatedBy'])
      ->whereNotNull('vendor_id')
      ->where('type', 'grn')
      ->doesntHave('events');

    if ($vendor_id) {
      $query->where('vendor_id', $vendor_id);
    }


    if ($date_from != '' && $date_to == '') {
      $query->whereDate('grn_date', '>=', $date_from);
    }
    if ($date_from == '' && $date_to != '') {
      $query->whereDate('grn_date', '<=', $date_to);
    }
    if ($date_from != '' && $date_to != '') {
      $query->whereDate('grn_date', '>=', $date_from);
      $query->whereDate('grn_date', '<=', $date_to);
    }

    $textSearchColumns = ['vendors.name', 'purchase_orders.id', 'purchase_orders.grn_date', 'purchase_orders.discount', 'purchase_orders.amount', 'purchase_orders.bill_no'];
    // Text search filter
    foreach ($textSearchValues as $key => $textSearchValue) {
      if (!empty($textSearchValue)) {
        $query->where(function ($q) use ($textSearchValue, $textSearchColumns) {
          // Search direct fields and vendor.name
          foreach ($textSearchColumns as $column) {
            if ($column === 'vendors.name') {
              $q->orWhereHas('vendor', function ($q2) use ($textSearchValue) {
                $q2->where('name', 'like', '%' . $textSearchValue . '%');
              });
            } else {
              $q->orWhere($column, 'like', '%' . $textSearchValue . '%');
            }
          }

          // Search vendor.contactPerson.contact_number
          $q->orWhereHas('vendor.contactPerson', function ($q3) use ($textSearchValue) {
            $q3->where('contact_number', 'like', '%' . $textSearchValue . '%');
          });
        });
      }
    }


    // Total and filtered counts
    $totalRecords = PurchaseOrder::with(['vendor.contactPerson', 'detail.item', 'detail.unitMeasure', 'currency', 'createdBy', 'updatedBy'])
      ->whereNotNull('vendor_id')
      ->where('type', 'grn')
      ->doesntHave('events')->count();
    $filteredRecords = $query->count();

    // Apply sorting
    // $orderableColumns = ['id', 'created_at']; // Define allowed columns
    // $orderByColumn = $orderableColumns[$orderBy] ?? 'id';
    $query->offset($offset)
      ->limit($limit)
      ->orderBy($orderBy, $orderType)
      ->get();

    // Apply pagination
    $orders = $query->skip($offset)->take($limit)->get();

    // // Add calculated fields (like PDF URLs & stitching summary)

    $orders->each(function ($result) {
      if (!empty($result->file_path)) {
        $parts = explode('_', $result->file_path);
        $folderName = array_slice($parts, 0, -2);
        $newFilename = implode('_', $folderName);
        $segments = explode('/', $newFilename);

        // Now, split the original filename by '/'
        $operation = explode('/', $result->file_path);
        $filePath =  $operation[0] . '/' . $operation[1] . '/' . $operation[2] . '/' . $operation[3];

        $filePaths = Storage::disk('public')->files('uploads/upload-bill/' . $operation[3]);

        $imageData = array_map(function ($file) {
          return [
            'url' => Storage::url($file),
            'type' => pathinfo($file, PATHINFO_EXTENSION),
          ];
        }, $filePaths);
        $result->imageData = $imageData;
        // dd($result->imageData);
      } else {
        $result->imageData = null;
      };
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
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $filteredRecords,
      'data' => $orders,
    ]);
  }

  public function approveOpenPurchaseOrder(string $id)
  {
    $places = Place::all();
    $currencies = Currency::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure')->find($id);

    return view($this->view, compact('result', 'places', 'currencies'));
  }

  public function storeApproveOpenPurchaseOrder(ApproveRequest $request, string $id)
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

  private function storeNewApproveOpenPurchaseOrder(array $request, string $id)
  {
    $random = rand(1000, 9999);
    $validated = $request;
    $approvedPurchaseOrderDetails = Arr::only($validated, ['items']);
    $vendor = Vendor::with('contactPerson', 'bank')->find($request['current_vendor']);
    $purchaseOrder =  PurchaseOrder::updateWithTransaction($id, [
      'status' => 'approved',
      'approved_by' => Auth::id(),
      'approved_file_name' => 'approved/' . $this->fileName($vendor, $random) . '.pdf'
    ]);
    foreach ($approvedPurchaseOrderDetails['items'] as $key => $approvedPurchaseOrderDetailItems) {
      $approvedPurchaseOrderDetailId = ApprovedPurchaseOrderDetail::createWithTransaction([
        'purchase_order_detail_id' => $approvedPurchaseOrderDetailItems['purchase_order_detail_id'],
        'quantity' => $approvedPurchaseOrderDetailItems['quantity'],
        'total' => $approvedPurchaseOrderDetailItems['total'],
      ]);
      Inventory::createWithTransaction([
        'approved_purchase_order_detail_id' => $approvedPurchaseOrderDetailId->id,
        'quantity' => $approvedPurchaseOrderDetailItems['quantity'],
        'remaining' => 0,
        'inventory_status' => 'Completed',
        'store_id' => $request['store_id'],
      ]);
      updateItemDetails(
        $approvedPurchaseOrderDetailItems['item_id'],
        [
          'received_quantity' => $approvedPurchaseOrderDetailItems['quantity']
        ]
      );
    }
    return true;
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
      return $this->createOrUpdateRecipeItem($purchaseOrder, $purchaseOrderItemData, $baseUOM, $itemQuantity, $selectedMeasure);
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

    return $this->createOrUpdateRecipeItem($purchaseOrder, $purchaseOrderItemData, $baseUOM, $convertedQuantity, $selectedMeasure);
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

    if (isset($purchaseOrderItemData['sub_total'])) {
      $data['sub_total'] = $purchaseOrderItemData['total'];
      $data['per_item_discount'] = $purchaseOrderItemData['per_item_discount'];
      $data['discount_option'] = $purchaseOrderItemData['discount_option'];
    }
    return PurchaseOrderDetail::createWithTransaction($data);
  }

  public function accessPermission($name)
  {
    $user = Auth::user();
    $userRole = $user->roles()->first();
    // dd($userRole->id);
    $role_id = $userRole->id;
    $route = Route::current();
    if ($route) {
      $moduleSlug = explode('/', $route->uri())[0];
      $currentModuleId = Module::where('slug', $moduleSlug)->pluck('id')->first();
      $hasPermission = Permission::with("userPermission", 'users', 'module')->where("module_id", $currentModuleId)
        ->where("name", $name)
        ->first();
      if ($hasPermission && $hasPermission->userPermission->isNotEmpty()) {
        return $hasPermission->userPermission->contains('id', $role_id);
      }
    }
    return false;
  }
  protected function formatUserName($validated)
  {
    // $user = User::findOrFail($validated['paid_by'] ?? $validated->paid_by);
    $user = User::findOrFail(Auth::id());
    $user_name = ucwords(Str::of($user->name)->lower());
    $user_name = str_replace(' ', '', $user_name);
    $formatted_user_name = 'FMB_' . $user->id . '_' . $user_name . '_' . date('Ymd_His');
    return $formatted_user_name;
  }
  protected function handleFileUploads($request, $documentsPath, $fileKey)
  {
    if ($request->hasFile($fileKey)) {
      foreach ($request->file($fileKey) as $file) {
        if ($file->isValid()) {
          $fileName = uniqid() . '_' . $file->getClientOriginalName();
          Storage::putFileAs($documentsPath, $file, $fileName);
        }
      }
    }
  }

  public function uploadBill(Request $request)
  {
    try {
      $purchaseOrder = PurchaseOrder::find($request->input('purchase_id'));
      $documentsPath = $purchaseOrder->file_path;

      DB::beginTransaction();
      $this->handleFileUploads($request, $documentsPath, 'upload_bill');
      DB::commit();
      return redirect()->route($this->redirect)->withInput()->with('success', '' . $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->withInput()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }
}
