<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Models\ItemCategory;
use App\Models\Item;
use App\Models\UnitMeasure;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\ApprovedPurchaseOrderDetail;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\ItemBaseUom;
use App\Models\Store;
use App\Models\ItemDetail;
use App\Models\Vendor;
use App\Models\VendorContactPerson;
use App\Models\Place;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\OrderByIdDescScope;

class MigrationController extends Controller
{
    public function inventories(Request $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            $sheetData = $data[0];
            $sheetDataWithoutHeader = array_slice($sheetData, 1);
            foreach ($sheetDataWithoutHeader as $fileInventory) {
                $fileCategory = $fileInventory[0];
                $fileItem = $fileInventory[1];
                $fileUnit = $fileInventory[2];
                $unitMeasure = UnitMeasure::where('short_form', $fileUnit)->first();
                $fileQuantity = (float) $fileInventory[4];
                $filePerUnitPrice = (float) str_replace('LKR', '', $fileInventory[5]);
                $fileTotal = $fileQuantity * $filePerUnitPrice;
                $itemCategory = ItemCategory::firstOrCreate([
                    'name' => $fileCategory,
                    'created_by' => 1
                ]);
                $item = Item::firstOrCreate([
                    'name' => $fileItem,
                    'category_id' => $itemCategory->id,
                    'created_by' => 1
                ]);
                ItemBaseUom::firstOrCreate([
                    'item_id' => $item->id,
                    'unit_measure_id' => $unitMeasure->id,
                    'created_by' => 1
                ]);
                $purchaseOrder = PurchaseOrder::create([
                    "place_id" => 2,
                    'currency_id' => 2,
                    "status" => "approved",
                    'amount' => $fileTotal,
                    "approved_by" => 1,
                    'type' => 'add',
                    'created_by' => 1
                ]);
                $approvedPurchaseOrderDetail = PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item->id,
                    'unit_measure_id' => $unitMeasure->id,
                    'select_unit_measure_id' => $unitMeasure->id,
                    'select_quantity' => $fileQuantity,
                    'quantity' => $fileQuantity,
                    'unit_price' => $filePerUnitPrice,
                    'total' => $fileTotal,
                    'created_by' => 1
                ]);
                $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::create([
                    'purchase_order_detail_id' => $approvedPurchaseOrderDetail->id,
                    'quantity' => $fileQuantity,
                    'total' => $fileTotal,
                    'created_by' => 1
                ]);
                $place = Place::where('name', $fileInventory[7])->first();
                $store = Store::where('place_id', $place->id)->where('floor', $fileInventory[8])->first();
                Inventory::create([
                    'approved_purchase_order_detail_id' => $approved_purchase_order_detail->id,
                    'quantity' => $fileQuantity,
                    'remaining' => 0,
                    'inventory_status' => 'Completed',
                    'store_id' => $store->id,
                    'created_by' => 1
                ]);
                $this->updateItemDetail(
                    $item->id,
                    [
                    'received_quantity' => $fileQuantity
                    ]
                );
            }
            DB::commit();
            return "Inventories Migrated";
        } catch(\Exception $e){
            throw $e;
            DB::rollback();
        }
    }

    public function vendors(Request $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            $sheetData = $data[0];
            $sheetDataWithoutHeader = array_slice($sheetData, 1);
            foreach ($sheetDataWithoutHeader as $fileVendors) {
                $vendor = Vendor::firstOrCreate([
                    'name' => $fileVendors[0],
                    'email' => ($fileVendors[5] != '') ? $fileVendors[5] : null,
                    'city_id' => 2,
                    'address' => ($fileVendors[3] != '') ? $fileVendors[3] : null,
                    'created_by' => 1
                ]);
                $vendorContactPerson = VendorContactPerson::firstOrCreate([
                    'vendor_id' => $vendor->id,
                    'name' => ($fileVendors[4] != '') ? $fileVendors[4] : $vendor->name,
                    'email' => ($fileVendors[5] != '') ? $fileVendors[5] : null,
                    'contact_number' => ($fileVendors[6] != '') ? $fileVendors[6] : null,
                    'primary' => '1',
                    'created_by' => 1
                ]);
            }
            DB::commit();
            return "Vendors Migrated";
        } catch(\Exception $e){
            return $e->getMessage();
            DB::rollback();
        }
    }

    public function stock(Request $request)
    {
        DB::beginTransaction();
        try {
            $arary = [];
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            $sheetData = $data[0];
            $sheetDataWithoutHeader = array_slice($sheetData, 1);
            foreach ($sheetDataWithoutHeader as $fileInventory) {
                $fileItem = $fileInventory[1];
                $fileQuantity = $fileInventory[3];
                $fileQuantity = filter_var($fileQuantity, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $fileQuantity = (float) $fileQuantity;
                $item = Item::where('name', $fileItem)->first();
                if ($item?->id) {
                    $approvedPurchaseOrderDetail = PurchaseOrderDetail::withoutGlobalScope(OrderByIdDescScope::class)->where('item_id', $item->id)->first();
                    $approvedPurchaseOrderDetail->select_quantity = $fileQuantity;
                    $approvedPurchaseOrderDetail->quantity = $fileQuantity;
                    $approvedPurchaseOrderDetail->total = $approvedPurchaseOrderDetail->unit_price * $fileQuantity;
                    $approvedPurchaseOrderDetail->save();
                    $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::withoutGlobalScope(OrderByIdDescScope::class)->where('purchase_order_detail_id', $approvedPurchaseOrderDetail->id)->first();
                    $approved_purchase_order_detail->quantity = $approvedPurchaseOrderDetail->quantity;
                    $approved_purchase_order_detail->total = $approvedPurchaseOrderDetail->total;
                    $approved_purchase_order_detail->save();
                    $inventory = Inventory::withoutGlobalScope(OrderByIdDescScope::class)->where('approved_purchase_order_detail_id', $approved_purchase_order_detail->id)->first();
                    $inventory->quantity = $approved_purchase_order_detail->quantity;
                    $inventory->save();
                    // $this->updateItemDetail(
                    //     $item->id,
                    //     [
                    //     'received_quantity' => $fileQuantity
                    //     ]
                    // );
                }
                else {
                    array_push($arary, $fileItem);
                }
            }
            DB::commit();
            return $arary;
        } catch(\Exception $e){
            throw $e;
            DB::rollback();
        }
    }

    public function refactorInventories(Request $request)
    {
        DB::beginTransaction();
        try {
            $arary = [];
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            $sheetData = $data[0];
            $sheetDataWithoutHeader = array_slice($sheetData, 1);
            foreach ($sheetDataWithoutHeader as $fileInventory) {
                $fileItem = $fileInventory[1];
                $fileQuantity = $fileInventory[4];
                $fileUnitPrice = $fileInventory[5];
                $fileQuantity = filter_var($fileQuantity, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $fileQuantity = (float) $fileQuantity;
                $item = Item::where('name', $fileItem)->first();
                if ($item?->id) {
                    $approvedPurchaseOrderDetail = PurchaseOrderDetail::withoutGlobalScope(OrderByIdDescScope::class)->where('item_id', $item->id)->first();
                    $approvedPurchaseOrderDetail->select_quantity = $fileQuantity;
                    $approvedPurchaseOrderDetail->quantity = $fileQuantity;
                    $approvedPurchaseOrderDetail->total = $fileUnitPrice * $fileQuantity;
                    $approvedPurchaseOrderDetail->save();
                    $approved_purchase_order_detail = ApprovedPurchaseOrderDetail::withoutGlobalScope(OrderByIdDescScope::class)->where('purchase_order_detail_id', $approvedPurchaseOrderDetail->id)->first();
                    $approved_purchase_order_detail->quantity = $approvedPurchaseOrderDetail->quantity;
                    $approved_purchase_order_detail->total = $approvedPurchaseOrderDetail->total;
                    $approved_purchase_order_detail->save();
                    $inventory = Inventory::withoutGlobalScope(OrderByIdDescScope::class)->where('approved_purchase_order_detail_id', $approved_purchase_order_detail->id)->first();
                    $inventory->quantity = $approved_purchase_order_detail->quantity;
                    $inventory->save();
                    // $this->updateItemDetail(
                    //     $item->id,
                    //     [
                    //     'received_quantity' => $fileQuantity
                    //     ]
                    // );
                }
                else {
                    array_push($arary, $fileItem);
                }
            }
            DB::commit();
            return $arary;
        } catch(\Exception $e){
            throw $e;
            DB::rollback();
        }
    }

    private function updateItemDetail(string $itemId, array $data)
    {

        $item = Item::with('detail')->find($itemId);
        if (isset($data['received_quantity'])) {
            $dataToCreate['available_quantity'] = $item?->detail?->available_quantity + $data['received_quantity'];
            $dataToCreate['received_quantity'] = $item?->detail?->received_quantity + $data['received_quantity'];
            $dataToCreate['issued_quantity'] = $item?->detail?->issued_quantity ?? 0;
            $dataToCreate['returned_quantity'] = $item?->detail?->returned_quantity ?? 0;
            $dataToCreate['supplier_returned_quantity'] = $item?->detail?->supplier_returned_quantity ?? 0;
            $dataToCreate['adjusted_quantity'] = $item?->detail?->adjusted_quantity ?? 0;
        }
        if (isset($data['issued_quantity'])) {
            $dataToCreate['available_quantity'] = $item?->detail?->available_quantity - $data['issued_quantity'];
            $dataToCreate['issued_quantity'] = $item?->detail?->issued_quantity + $data['issued_quantity'];
            $dataToCreate['received_quantity'] = $item?->detail?->received_quantity ?? 0;
            $dataToCreate['returned_quantity'] = $item?->detail?->returned_quantity ?? 0;
            $dataToCreate['supplier_returned_quantity'] = $item?->detail?->supplier_returned_quantity ?? 0;
            $dataToCreate['adjusted_quantity'] = $item?->detail?->adjusted_quantity ?? 0;
        }
        if (isset($data['returned_quantity'])) {
            $dataToCreate['available_quantity'] = $item?->detail?->available_quantity + $data['returned_quantity'];
            $dataToCreate['returned_quantity'] = $item?->detail?->returned_quantity + $data['returned_quantity'];
            $dataToCreate['received_quantity'] = $item?->detail?->received_quantity ?? 0;
            $dataToCreate['issued_quantity'] = $item?->detail?->issued_quantity ?? 0;
            $dataToCreate['supplier_returned_quantity'] = $item?->detail?->supplier_returned_quantity ?? 0;
            $dataToCreate['adjusted_quantity'] = $item?->detail?->adjusted_quantity ?? 0;
        }
        if (isset($data['supplier_returned_quantity'])) {
            $dataToCreate['available_quantity'] = $item?->detail?->available_quantity - $data['supplier_returned_quantity'];
            $dataToCreate['supplier_returned_quantity'] = $item?->detail?->supplier_returned_quantity + $data['supplier_returned_quantity'];
            $dataToCreate['received_quantity'] = $item?->detail?->received_quantity ?? 0;
            $dataToCreate['issued_quantity'] = $item?->detail?->issued_quantity ?? 0;
            $dataToCreate['returned_quantity'] = $item?->detail?->returned_quantity ?? 0;
            $dataToCreate['adjusted_quantity'] = $item?->detail?->adjusted_quantity ?? 0;
        }
        if (isset($data['adjusted_quantity'])) {
            $dataToCreate['available_quantity'] = $item?->detail?->available_quantity - $data['adjusted_quantity'];
            $dataToCreate['adjusted_quantity'] = $item?->detail?->adjusted_quantity + $data['adjusted_quantity'];
            $dataToCreate['received_quantity'] = $item?->detail?->received_quantity ?? 0;
            $dataToCreate['issued_quantity'] = $item?->detail?->issued_quantity ?? 0;
            $dataToCreate['returned_quantity'] = $item?->detail?->returned_quantity ?? 0;
            $dataToCreate['supplier_returned_quantity'] = $item?->detail?->supplier_returned_quantity ?? 0;
        }
        $dataToCreate['item_id'] = $item->id;
        $dataToCreate['created_by'] = 1;
        ItemDetail::create(
            $dataToCreate
        );
        return true;
    }
}
