<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class AddPurchaseOrderRequest extends FormRequest
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
            'place_id' => 'required|integer|exists:places,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'amount' => 'required|numeric|min:1',
            'discount' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.event_id' => 'numeric|min:1',
            'items.*.recipe_id' => 'nullable|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.unit_price' => 'numeric|min:1',
            'items.*.unit_id' => 'integer|exists:unit_measures,id',
            'items.*.quantity_hidden' => 'numeric|gt:0',
            'items.*.quantity' => 'numeric|gt:0|lte:items.*.quantity_hidden',
            'items.*.total' => 'numeric|min:1',
            'events' => 'required|array',
            'events.*' => 'integer|exists:events,id', 
        ];
    }
}
