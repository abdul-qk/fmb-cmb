<?php

namespace App\Http\Requests\Event;

use App\Rules\MonthlyDateTime;
use Illuminate\Foundation\Http\FormRequest;

class MonthlyRequest extends FormRequest
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
    // Initialize the rules array
    $rules = [
      'items' => 'required|array',
    ];

    foreach ($this->input('items', []) as $key => $item) {
      // Determine start and end times based on serving type
      if ($item['serving'] === 'thaal') {
        $start = "20:30:00";
        $end = "22:30:00";
      } else {
        $start = "07:00:00";
        $end = "10:00:00";
      }

      // Add item-specific validation rules
      $rules["items.$key.date"] = [
        'required',
        'date_format:Y-m-d',
        new MonthlyDateTime($start, $end, $item['date'], false, $item['serving']),
      ];
      $rules["items.$key.serving"] = ['required', 'string'];
      $rules["items.$key.name"] = ['required', 'string'];
      $rules["items.$key.description"] = ['nullable', 'string'];
      if (isset($item['tiffin_id'])) {
        $rules["items.$key.tiffin_id"] = ['nullable', 'string'];
        $rules["items.$key.item_id"] = ['nullable', 'string'];
      }
      if (isset($item['thaal_id'])) {
        $rules["items.$key.thaal_id"] = ['nullable', 'string'];
      }
    }

    return $rules;
  }
}
