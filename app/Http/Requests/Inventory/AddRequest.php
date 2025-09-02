<?php

namespace App\Http\Requests\Inventory;

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
          'event_id' => 'required|integer|exists:events,id',
          'kitchen_id' => 'required|integer|exists:kitchens,id',
          'store_id' => 'required|integer|exists:stores,id',
          'received_by' => 'required|integer',
          'selected_items' => 'required|array',
          'issue_unit' => 'nullable|array',
          'unit_id' => 'nullable|array',
          'note' => 'nullable|string',
          'other' => 'nullable',
          'remaining_quantity.*' => 'required|numeric',
          'quantity.*' => 'required|numeric',
      ];
    }
}
