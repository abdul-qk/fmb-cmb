<?php

namespace App\Http\Requests\UomConversion;

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
    $unitMeasureId = $this->route('uom_conversion');

    return [
      'base_uom' => [
        'required',
        'different:secondary_uom',
        Rule::unique('uom_conversions')->where(function ($query) {
          return $query->where('secondary_uom', request('secondary_uom'));
        })->ignore($unitMeasureId),
      ],
      'secondary_uom' => [
        'required',
        Rule::unique('uom_conversions')->where(function ($query) {
          return $query->where('base_uom', request('base_uom'));
        })->ignore($unitMeasureId),
      ],
      'conversion_value' => 'required|numeric',
    ];
  }
}
