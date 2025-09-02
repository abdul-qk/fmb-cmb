<?php

namespace App\Http\Requests\Event;

use App\Rules\EventDateTime;
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
    foreach ($this->input('items', []) as $key => $item) {
      // Determine start and end times based on serving type
      if ($item['serving'] === 'thaal') {
        $start = "20:30:00";
        $end = "22:30:00";
      } else {
        $start = "07:00:00";
        $end = "10:00:00";
      }
      return [
        'items' => 'required|array',
        'items.*.date' => [
          'required',
          'date_format:Y-m-d',
          new MonthlyDateTime($start, $end, $this->input('items.*.date'), false, $this->input('items.*.serving')),
        ],
        'items.*.serving' => ['required', 'string'],
        'items.*.name' => ['required', 'string'],
        'items.*.description' => ['nullable', 'string'],
        'items.*.tiffin_id' => ['nullable', 'string'],
        'items.*.thaal_id' => ['nullable', 'string'],
        'items.*.item_id' => ['nullable', 'string'],
      ];
    }
  }
}
