<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class EditOpenPurchaseOrderRequest extends FormRequest
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
            'current_vendor' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:1',
            'place_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.unit_price' => 'numeric|min:1',
            'items.*.unit_id' => 'integer|exists:unit_measures,id',
            'items.*.quantity' => 'numeric|gt:0',
            'items.*.total' => 'numeric|min:1',
        ];
    }
}
