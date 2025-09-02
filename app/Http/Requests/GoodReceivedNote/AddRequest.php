<?php

namespace App\Http\Requests\GoodReceivedNote;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'vendor_id' => 'required|integer|exists:vendors,id',
            'store_id' => 'required|integer|exists:stores,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'grn_date' => 'required|string',
            // 'paid_by' => 'required|string',
            'bill_no' => 'required|string',
            'upload_bill.*' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
            'sub_amount' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'additional_charges' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric',
            'description' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.unit_price' => 'numeric|min:0',
            'items.*.unit_id' => 'integer|exists:unit_measures,id',
            'items.*.quantity' => 'numeric|gt:0',
            'items.*.total' => 'numeric|min:0',
            'items.*.sub_total' => 'numeric|min:0',
            'items.*.per_item_discount' => 'nullable|numeric|min:0',
            'items.*.discount_option' => 'string',
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
            $attributes["items.$index.quantity"] = "Quantity for $itemName";
            $attributes["items.$index.unit_id"] = "UOM for $itemName";
            $attributes["items.$index.unit_price"] = "Unit Price for $itemName";
            $attributes["items.$index.per_item_discount"] = "Item Discount for $itemName";
            $attributes["items.$index.discount_option"] = "Discount Option for $itemName";
            $attributes["items.$index.total"] = "Total for $itemName";
            $attributes["items.$index.sub_total"] = "Sub Total for $itemName";
        }

        return $attributes;
    }
}
