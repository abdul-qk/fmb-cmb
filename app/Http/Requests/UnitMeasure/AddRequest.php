<?php

namespace App\Http\Requests\UnitMeasure;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class AddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allowing all users to make this request, modify as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unitMeasureId = $this->route('unit_of_measure'); // Get the current unit measure ID

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('unit_measures', 'name')->ignore($unitMeasureId),
            ],
            'short_form' => [
                'required',
                'string',
                Rule::unique('unit_measures', 'short_form')->ignore($unitMeasureId),
            ],
        ];
    }
}

