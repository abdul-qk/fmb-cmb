<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\ModuleApiController;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\CityApiController;
use App\Http\Controllers\Api\PlaceApiController;
use App\Http\Controllers\Api\StoreApiController;
use App\Http\Controllers\Api\KitchenApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\Api\RecipeApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\ItemCategoryApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Core Entities
Route::apiResource('users', UserApiController::class);
Route::apiResource('roles', RoleApiController::class);
Route::apiResource('modules', ModuleApiController::class);

// Location Entities
Route::apiResource('countries', CountryApiController::class);
Route::apiResource('cities', CityApiController::class);
Route::apiResource('places', PlaceApiController::class);
Route::apiResource('stores', StoreApiController::class);
Route::apiResource('kitchens', KitchenApiController::class);

// Event Entities
Route::apiResource('events', EventApiController::class);
// Custom menu routes must come before resource routes
Route::get('/menus/dishes-by-week', [MenuApiController::class, 'dishesByWeek']);
Route::post('/menus/user-selections', [MenuApiController::class, 'saveUserSelections']);
Route::get('/menus/user-selections', [MenuApiController::class, 'getUserSelections']);
Route::get('/menus/fetch-menu-data', [MenuApiController::class, 'fetchMenuData']);
Route::apiResource('menus', MenuApiController::class);
Route::apiResource('recipes', RecipeApiController::class);

// Procurement Entities
Route::apiResource('purchase-orders', PurchaseOrderApiController::class);
Route::apiResource('vendors', VendorApiController::class);

// Inventory Entities
Route::apiResource('inventories', InventoryApiController::class);
Route::apiResource('items', ItemApiController::class);
Route::apiResource('item-categories', ItemCategoryApiController::class);
