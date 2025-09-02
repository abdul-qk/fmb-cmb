<?php

use App\Http\Controllers\AdjustmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateSession;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FetchController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UnitMeasureController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupplyCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TiffinSizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\VendorContactPersonController;
use App\Http\Controllers\GoodsReceivedNoteController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemBaseUomController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuItemQuantitiesController;
use App\Http\Controllers\MonthlyEventController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\OpenPurchaseOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorBankController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ServingQuantityController;
use App\Http\Controllers\UOMConversionController;
use App\Http\Controllers\GoodsIssuedNoteController;
use App\Http\Controllers\GoodsReturnedController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\IssuedToKitchenController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\MigrationController;
use App\Models\SupplierReturn;

Route::get('/', function () {
    return view('auth.login');
});
Route::post('/login', [AuthController::class, 'login']);
Route::middleware([ValidateSession::class])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resources([
        'modules' => ModuleController::class,
        'cuisines' => CuisineController::class,
        'dishes' => DishController::class,
        'unit-of-measures' => UnitMeasureController::class,
        'uom-conversions' => UOMConversionController::class,
        'items' => ItemController::class,
        'item-base-uoms' => ItemBaseUomController::class,
        'item-categories' => ItemCategoryController::class,
        'serving-quantities' => ServingQuantityController::class,
        'recipes' => RecipeController::class,
        'events' => EventController::class,
        'menus' => MenuController::class,
        'menu-items' => MenuItemController::class,
        'inventories' => InventoryController::class,
        'locations' => LocationController::class,
        'stores' => StoreController::class,
        'places' => PlaceController::class,
        'kitchens' => KitchenController::class,
        'tiffin-sizes' => TiffinSizeController::class,
        'educations' => EducationController::class,
        'designations' => DesignationController::class,
        'profiles' => ProfileController::class,
        'vendors' => VendorController::class,
        'countries' => CountryController::class,
        'cities' => CityController::class,
        'currencies' => CurrencyController::class,
        'roles' => RoleController::class,
        'users' => UserController::class,
        'permissions' => PermissionController::class,
        'vendor-contact-persons' => VendorContactPersonController::class,
        'vendor-banks' => VendorBankController::class,
        'open-purchase-orders' => OpenPurchaseOrderController::class,
        'goods-received-notes' => GoodsReceivedNoteController::class,
        'purchase-orders' => PurchaseOrderController::class,
        // 'goods-issued-notes' => GoodsIssuedNoteController::class,
        'issued-to-kitchens' => IssuedToKitchenController::class,
        'goods-returned' => GoodsReturnedController::class,
        'adjustment' => AdjustmentController::class,
        'supplier-return' => SupplierReturnController::class,
    ]);
    Route::prefix('inventories')->group(function () {
        Route::controller(InventoryController::class)->group(function () {
        Route::get('add/{addId}', 'add')->name('inventories.add');
        Route::put('store-add/{addId}', 'storeAdd')->name('inventories.add.store');
        Route::get('supplier/return', 'supplierReturn')->name('inventories.return');
        Route::post('store-supplier-return', 'storeSupplierReturn')->name('inventories.store.return');
        Route::get('adjustment/return', 'adjustment')->name('inventories.adjustment');
        Route::post('store-adjustment', 'storeAdjustment')->name('inventories.store.adjustment');
        Route::get('transfer/{id}', 'transfer')->name('inventories.transfer');
        Route::put('transfer-store/{id}', 'storeTransfer')->name('inventories.transfer.store');
        Route::get('edit-inventory/{id}', 'editInventory')->name('inventories.edit-inventory');
        Route::put('update-inventory/{id}', 'updateInventory')->name('inventories.edit-inventory.update');
        });
    });
    Route::prefix('menus')->group(function () {
        Route::controller(MenuController::class)->group(function () {
            Route::get('approve/{menuId}', 'approve')->name('menus.approve');
            Route::put('store-approve/{menuId}', 'storeApprove')->name('menus.approve.store');
            Route::get('chef-input/{menuId}', 'chefInput')->name('menus.chef-input');
            Route::post('store-chef-input/{menuId}', 'storeChefInput')->name('menus.chef-input.store');
            Route::get('edit-chef-input/{menuId}', 'editChefInput')->name('menus.edit-chef-input');
            Route::post('update-chef-input/{menuId}', 'updateChefInput')->name('menus.chef-input.update');
        });
    });
    Route::prefix('open-purchase-orders')->group(function () {
        Route::controller(OpenPurchaseOrderController::class)->group(function () {
            Route::get('approve/{purchaseOrderId}', 'approveOpenPurchaseOrder')->name('open_purchase_orders.approve');
            Route::put('store-approve/{purchaseOrderId}', 'storeApproveOpenPurchaseOrder')->name('open_purchase_orders.approve.store');
            Route::put('reject/{purchaseOrderId}', 'rejectOpenPurchaseOrder')->name('open_purchase_orders.reject');
        });
    });
    Route::prefix('goods-received-notes')->group(function () {
      Route::controller(GoodsReceivedNoteController::class)->group(function () {
        Route::put('upload/{id}', 'uploadBill')->name('goods-received-notes.upload');
      });
    });
    Route::prefix('purchase-orders')->group(function () {
        Route::controller(PurchaseOrderController::class)->group(function () {
            Route::get('approve/{purchaseOrderId}', 'approvePurchaseOrder')->name('purchase_orders.approve');
            Route::put('store-approve/{purchaseOrderId}', 'storeApprovePurchaseOrder')->name('purchase_orders.approve.store');
            Route::put('reject/{purchaseOrderId}', 'rejectPurchaseOrder')->name('purchase_orders.reject');
        });
    });
    Route::prefix('events')->group(function () {
        Route::controller(EventController::class)->group(function () {
            Route::put('approve/{eventId}', 'approveEvent')->name('events.approve');
            Route::put('reject/{eventId}', 'rejectEvent')->name('events.reject');
            Route::get('monthly/{id}', 'monthlyEvent')->name('events.monthly');
            Route::post('store-monthly-event', 'monthlyEventStore')->name('events.monthly.store');
            Route::get('create-menu/{eventId}', 'createMenu')->name('events.create-menu');
            Route::post('store-menu/{eventId}', 'storeMenu')->name('events.store-menu');
            Route::get('create-chef-menu/{eventId}', 'createChefMenu')->name('events.create-chef-menu');
            Route::post('store-chef-menu/{eventId}', 'storeChefMenu')->name('events.store-chef-menu');
        });
    });
    Route::prefix('procurement-requisition')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('', 'procurementRequisition')->name('procurement-requisition.index');
        });
    });
    Route::prefix('event-consumption')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('', 'eventConsumption')->name('event-consumption.index');
        });
    });
    Route::prefix('inventory-summary')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('', 'inventorySummary')->name('inventory-summary.index');
        });
    });
    Route::get('fetch-event-item-list', [FetchController::class, 'fetchEventItemList'])->name('fetchEventItemList');
    Route::get('fetch-event-items', [FetchController::class, 'fetchEventItems'])->name('fetchEventItems');
    Route::get('fetch-return-from-kitchen-items', [FetchController::class, 'fetchReturnFromKitchenItems'])->name('fetchReturnFromKitchenItems');
    Route::get('fetch-vendor-event-items', [FetchController::class, 'fetchVendorEventItems'])->name('fetchVendorEventItems');
    Route::get('fetch-vendor-items', [FetchController::class, 'fetchVendorItems'])->name('fetchVendorItems');
    Route::get('fetch-dish-category', [FetchController::class, 'fetchDishCategory']);
    Route::get('fetch-get-ingredient', [FetchController::class, 'getIngredient']);
    Route::get('fetch-get-ingredient-chef', [FetchController::class, 'getIngredientChef']);
    Route::get('fetch-get-item', [FetchController::class, 'getItem']);
    Route::get('fetch-get-cities', [FetchController::class, 'cities']);
    Route::get('fetch-get-supply-category', [FetchController::class, 'getSupplyCategory']);
    Route::get('fetch-module-display-order', [FetchController::class, 'fetchModuleDisplayOrder'])->name('fetchModuleDisplayOrder');
    Route::get('/fetch-misc-documents/{filename}', [FetchController::class, 'miscDocuments']);
    Route::get('/fetch-medical-documents/{filename}', [FetchController::class, 'medicalDocuments']);
    Route::get('/fetch-national-identity/{filename}', [FetchController::class, 'nationalIdentityDocuments']);
    Route::get('fetch-file-delete', [FetchController::class, 'deleteFile']);
    Route::get('fetch-unit', [FetchController::class, 'fetchUnit']);
    Route::get('fetch-uom-base', [FetchController::class, 'fetchUomBase']);
    Route::get('fetch-item-details', [FetchController::class, 'fetchItemDetails']);
    Route::get('fetch-adjustment-items', [FetchController::class, 'fetchAdjustmentItems'])->name('fetchAdjustmentItems');
    Route::get('fetch-event-item-details-for-kitchen', [FetchController::class, 'fetchEventItemDetailsForKitchen'])->name('fetchEventItemDetailsForKitchen');
    Route::get('fetch-grn-items', [FetchController::class, 'fetchGrnItems'])->name('fetchGrnItems');
    Route::get('fetch-monthly-events-year', [FetchController::class, 'getServingQuantityIdsWithinCurrentMonth'])->name('getServingQuantityIdsWithinCurrentMonth');
    Route::get('/goods-received-notes-data', [GoodsReceivedNoteController::class, 'datatableListing'])->name('goods-received-notes.datatableListing');
});
Route::prefix('migrate')->group(function () {
    Route::post('inventories', [MigrationController::class, 'inventories'])->name('inventories');
    Route::post('vendors', [MigrationController::class, 'vendors'])->name('vendors');
    Route::post('stock', [MigrationController::class, 'stock'])->name('stock');
    Route::post('refactor-inventories', [MigrationController::class, 'refactorInventories'])->name('refactorInventories');
});
