<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Models\Item;
use App\Models\ItemDetail;

if (!function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs()
    {
        $segments = request()->segments();
        $breadcrumbs = [];
        $url = '';

        foreach ($segments as $index => $segment) {
            // Skip numeric segments (IDs)
            if (is_numeric($segment)) {
                continue;
            }

            // Build the URL up to the current segment
            $url .= '/' . $segment;

            // Convert segment name to a readable format
            $segmentName = ucfirst(str_replace('-', ' ', $segment));

            $breadcrumbs[] = [
                'name' => $segmentName,
                'url' => url($url),
                'active' => $index == count($segments) - 1, // Mark the last breadcrumb as active
            ];
        }

        return $breadcrumbs;
    }
}

if (!function_exists('hasPermissionForModule')) {
    function hasPermissionForModule($permissionName, $moduleId)
    {
        if(auth()->user()->hasRole('developer')){
            return true;
        }
        if(!auth()->user()->roles()->first()){
            abort(403, 'No Role Assigned');
        }
        return auth()->user()->roles()->first()->permissions()->where(['name' => $permissionName, 'module_id' => $moduleId])->exists();
    }
}

if (!function_exists('updateItemDetails')) {
    function updateItemDetails(string $itemId, array $data)
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
        ItemDetail::createWithTransaction(
            $dataToCreate
        );
        return true;
    }
}