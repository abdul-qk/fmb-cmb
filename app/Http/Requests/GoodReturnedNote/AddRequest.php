<?php

namespace App\Http\Requests\GoodReturnedNote;

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
        // return [
        //     'kitchen_id' => 'required|integer|exists:kitchens,id',
        //     'event_id' => 'required|integer|exists:events,id',
        //     'return_by' => 'required|integer',
        //     'items' => 'required|array|min:1',
        //     'items.*.item_id' => 'required|integer|exists:items,id',
        //     'items.*.item_name' => 'required|string',
        //     'items.*.item_uom' => 'required|string',
        //     'items.*.issued_quantity' => 'required|numeric|gt:0',
        //     'items.*.returned_quantity' => 'required|numeric|gt:0|lte:items.*.issued_quantity',
        //     'items.*.reason' => 'nullable|string|max:255'
        // ];
        return [
            'kitchen_id' => 'required|integer|exists:kitchens,id',
            'event_id' => 'required|integer|exists:events,id',
            'return_by' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.inventory_detail_id' => 'required|integer|exists:inventory_details,id',
            'items.*.base_unit' => 'required|string',
            'items.*.unit_id' => 'required|integer|exists:unit_measures,id',
            'items.*.issued_quantity' => 'required|numeric|gt:0',
            'items.*.quantity' => 'required|numeric|gt:0|lte:items.*.issued_quantity',
            'items.*.reason' => 'nullable|string|max:255',
        ];
    }
}
