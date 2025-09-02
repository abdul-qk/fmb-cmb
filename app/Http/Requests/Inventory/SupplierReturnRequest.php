<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;

class SupplierReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grn_id' => 'required|integer|exists:purchase_orders,id',
            'vendor_id' => 'required|integer|exists:vendors,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.base_unit' => 'required|string',
            'items.*.unit_id' => 'required|integer|exists:unit_measures,id',
            'items.*.available_quantity' => 'required|numeric|gt:0',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.reason' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        // Fetch items to map their names
        $items = $this->input('items', []);
        foreach ($items as $index => $item) {
            $itemName = Item::find($item['item_id'])->name ?? 'Item';
            $attributes["items.$index.item_id"] = $itemName;
            $attributes["items.$index.base_unit"] = "UOM for $itemName";
            $attributes["items.$index.available_quantity"] = "Available quantity for $itemName";
            $attributes["items.$index.unit_id"] = "Return UOM for $itemName";
            $attributes["items.$index.quantity"] = "Return Quantity for $itemName";
        }

        return $attributes;
    }
}
