<?php

namespace App\Http\Requests\GoodReturnedNote;

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
            'item_id' => 'required|integer|exists:items,id',
            'issued_quantity' => 'required|numeric|gt:0',
            'returned_quantity' => 'required|numeric|gt:0|lte:issued_quantity',
            'reason' => 'nullable|string|max:255',
            'return_by' => 'required|integer',
        ];
    }
}
