<?php

namespace App\Http\Requests\GoodReceivedNote;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'store_id' => 'required|integer',
            'items' => 'nullable|array|min:0',
            'items.*.purchase_order_detail_id' => 'nullable|integer',
            'items.*.approved_purchase_order_detail_id' => 'nullable|integer',
            'items.*.current_quantity' => 'nullable|numeric',
            'items.*.quantity' => 'numeric|gt:0',
            'items.*.item_id' => 'required|integer|exists:items,id',
        ];
    }
}
