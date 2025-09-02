<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class AdjustmentRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.base_unit_id' => 'required|integer|exists:unit_measures,id',
            'items.*.unit_id' => 'required|integer|exists:unit_measures,id',
            'items.*.available_quantity' => 'required|numeric|gt:0',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.reason' => 'nullable|string|max:255',
        ];
    }
}
