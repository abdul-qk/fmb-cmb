<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseOrder\AddPurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\EditPurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\ApprovePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Place;
use App\Models\UnitMeasure;
use App\Models\User;
use App\Models\Currency;
use App\Models\Menu;
use App\Models\MenuServing;
use App\Models\RecipeItem;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $results = PurchaseOrder::with(['vendor', 'detail.item', 'detail.unitMeasure', 'events', 'currency', 'createdBy', 'updatedBy'])
    ->whereNotNull('vendor_id')
    ->where('type', 'po')
    ->has('events')
    ->get();
    $pdfUrl = '';

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
    $user = User::with('place')
    ->find(Auth::id());
    $events = Event::query()
    ->withWhereHas('menus')
    ->get();
    // ->withWhereHas('menus.menuServings.recipeItem')
    // dd();
    // ->flatMap(function ($event)  {
    //   return $event->menus->flatMap(function ($menu) use ($event) {
    //     return $menu->menuServings->map(function ($serving) use ($event) {

    //       $purchaseOrderDetail = PurchaseOrderDetail::withWhereHas('approvedDetail')
    //       ->where('event_id', $event->id)
    //       ->where('item_id', $serving->recipeItem->item_id)
    //       ->first();
    //         // if ($purchaseOrderDetail?->approvedDetail?->quantity < $serving->recipeItem->item_quantity) {
    //           return Event::find($event->id);
    //         // }
    //     });
    //   });
    // })->filter()->unique('id')->values();

    $vendors = Vendor::all();
    $items = Item::all();
    $places = Place::all();
    $units = UnitMeasure::all();
    $currencies = Currency::all();
    return view($this->view, compact('vendors', 'items', 'units', 'places', 'events', 'currencies', 'user'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(AddPurchaseOrderRequest $request)
  {
    $random = rand(1000, 9999);
    $validated = $request->validated();
    $validatedEvents = Arr::only($validated, ['events']);
    $validated = Arr::except($validated, ['events']);
    $purchaseOrderData = Arr::except($validated, ['events', 'items']);
    $purchaseOrderItemsData = Arr::only($validated, ['items']);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($validated['vendor_id']);
      $purchaseOrderData['file_name'] = 'create/' . $this->fileName($vendor, $random) . '.pdf';
      $purchaseOrderData['type'] = 'po';
      $purchaseOrder = PurchaseOrder::createWithTransaction($purchaseOrderData);
      $this->generatePDF($validated, $purchaseOrder, 'create', $vendor, $random);
      foreach ($purchaseOrderItemsData['items'] as $key => $purchaseOrderItemData) {
        PurchaseOrderDetail::createWithTransaction([
          'purchase_order_id' => $purchaseOrder->id,
          'event_id' => $purchaseOrderItemData['event_id'],
          'recipe_id' => $purchaseOrderItemData['recipe_id'],
          'item_id' => $purchaseOrderItemData['item_id'],
          'unit_measure_id' => $purchaseOrderItemData['unit_id'],
          'quantity' => $purchaseOrderItemData['quantity'],
          'unit_price' => $purchaseOrderItemData['unit_price'],
          'total' => $purchaseOrderItemData['total'],
        ]);
      }
      $purchaseOrder->events()->sync($validatedEvents['events']);
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
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'detail.event', 'place', 'currency', 'events')->find($id);
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


    return view($this->view, compact('result', 'places', 'pdfUrl','approvedPdfUrl'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $user = User::with('place')
    ->find(Auth::id());
    $currencies = Currency::all();
    $places = Place::all();
    $vendors = Vendor::all();
    $events = Event::all();
    $items = Item::all();
    $units = UnitMeasure::all();
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'detail.event', 'events')->find($id);
    $selectedEvents = $result->events->pluck('id')->toArray();

    $pdfUrl = '';
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
    return view($this->view, compact('result', 'items', 'units', 'vendors', 'places', 'pdfUrl', 'events', 'selectedEvents', 'currencies', 'user'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(EditPurchaseOrderRequest $request, string $id)
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
        PurchaseOrderDetail::createWithTransaction([
          'purchase_order_id' => $purchaseOrder->id,
          'event_id' => $purchaseOrderItemData['event_id'],
          'item_id' => $purchaseOrderItemData['item_id'],
          'unit_measure_id' => $purchaseOrderItemData['unit_id'],
          'quantity' => $purchaseOrderItemData['quantity'],
          'unit_price' => $purchaseOrderItemData['unit_price'],
          'total' => $purchaseOrderItemData['total'],
        ]);
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

  public function approvePurchaseOrder(string $id)
  {
    $result = PurchaseOrder::with('vendor', 'detail.item', 'detail.unitMeasure', 'detail.event', 'place', 'currency', 'events')->find($id);
    return view($this->view, compact('result'));
  }

  public function storeApprovePurchaseOrder(ApprovePurchaseOrderRequest $request, string $id)
  {
    $validated = $request->validated();
    $approvedPurchaseOrderDetails = Arr::only($validated, ['items']);
    $random = rand(1000, 9999);
    try {
      DB::beginTransaction();
      $vendor = Vendor::with('contactPerson', 'bank')->find($request->current_vendor);
      foreach ($approvedPurchaseOrderDetails['items'] as $key => $approvedPurchaseOrderDetail) {
        $event = Event::find($approvedPurchaseOrderDetail['event_id']);
        $item = Item::find($approvedPurchaseOrderDetail['item_id']);
        $detail = PurchaseOrderDetail::withWhereHas('approvedDetail')
        ->where('event_id', $approvedPurchaseOrderDetail['event_id'])
        ->where('item_id', $approvedPurchaseOrderDetail['item_id'])
        ->get();
        $menus = Menu::withWhereHas('recipes.recipeItems', function ($query) use ($item) {
          $query->where('item_id', $item->id);
        })
        ->where('event_id', $event->id)
        ->get()
        ->flatMap(function ($menu) use ($event) {
          return $menu->recipes->flatMap(function ($recipe) use ($event) {
            return $recipe->recipeItems->map(function ($recipeItem) use ($event, $recipe) {
              $recipeItem->adjusted_quantity = ($event->serving_persons / $recipe->serving) * $recipeItem->item_quantity;
              return $recipeItem;
            });
          });
        });
        $requiredEventItemQuantity = $menus->sum('adjusted_quantity');
        $sum = $detail->sum(fn($item) => $item?->approvedDetail?->quantity);
        if (($sum + $approvedPurchaseOrderDetail['quantity']) > $requiredEventItemQuantity) {
          DB::rollBack();
          return redirect()->back()->withInput()->with('error', 'Allowed quantity of ' . $item->name . ' for event ' . $event->name . ' is ' . $requiredEventItemQuantity - $sum);
        }
        $purchaseOrder = PurchaseOrder::updateWithTransaction($id, ['status' => 'approved', 
          'approved_by' => Auth::id(),
          'approved_file_name' => 'approved/' . $this->fileName($vendor, $random) . '.pdf'
        ]);
        $this->generatePDF($validated, $purchaseOrder, "approved", $vendor, $random);
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

  public function rejectPurchaseOrder(string $id)
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
    $place = Place::with('location.country.city')->find(Auth::user()->place_id ?? $record['place_id']);
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
}
