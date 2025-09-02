<?php

namespace App\Http\Requests\Place;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'name' => 'required|string',
      'contact_no' => [
        'required',
        'regex:/^\+\d{1,3}\d{6,14}$/',
        Rule::unique('places', 'contact_no')->ignore($this->route('place'))
      ],
      'location_id' => 'required|numeric',
      'description' => 'nullable|string'
    ];
  }
}
