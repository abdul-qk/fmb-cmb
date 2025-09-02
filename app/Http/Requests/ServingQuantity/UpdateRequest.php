<?php

namespace App\Http\Requests\ServingQuantity;

use App\Models\ServingQuantity;
use App\Models\ServingQuantityTiffin;
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
    $tiffinRules = [
      'items' => 'required|array',
      'items.*.id' => ['required', 'numeric'],
      'items.*.quantity' => ['required', 'numeric'],
      'items.*.person_no' => ['required', 'numeric'],
      'items.*.tiffin_size_id' => ['required', 'string'],
      'items.*.date_from' => [
        'required',
        'date',
        function ($attribute, $value, $fail) {
          $this->checkDateOverlap($attribute, $value, $fail);
        },
      ],
      'items.*.date_to' => [
        'required',
        'date',
        'after_or_equal:items.*.date_from',
        function ($attribute, $value, $fail) {
          $this->checkDateOverlap($attribute, $this->input("items." . filter_var($attribute, FILTER_SANITIZE_NUMBER_INT) . ".date_from"), $fail);
        },
      ],
    ];

    $commonRules = [
      'serving' => ['required', 'in:Thaal,Tiffin'],
      'quantity' => ['nullable'],
      'serving_person' => ['nullable'],
      'date_from' => [
        'nullable',
        'date',
        function ($attribute, $value, $fail) {
          $dateTo = $this->date_to;
          $existing = ServingQuantity::where('serving', $this->serving)
            ->where('id', '<>', $this->route('serving_quantity')) // Ignore the current record being updated
            ->where(function ($query) use ($value, $dateTo) {
              $query->whereBetween('date_from', [$value, $dateTo])
                ->orWhereBetween('date_to', [$value, $dateTo])
                ->orWhere(function ($query) use ($value, $dateTo) {
                  $query->where('date_from', '<=', $value)
                    ->where('date_to', '>=', $dateTo);
                });
            })->exists();

          if ($existing) {
            $fail('The selected date range overlaps with an existing entry for this serving type.');
          }
        },
      ],
      'date_to' => [
        'nullable',
        'date',
        'after_or_equal:date_from',
        function ($attribute, $value, $fail) {
          $dateFrom = $this->date_from;
          $existing = ServingQuantity::where('serving', $this->serving)
            ->where('id', '<>', $this->route('serving_quantity')) // Ignore the current record being updated
            ->where(function ($query) use ($value, $dateFrom) {
              $query->whereBetween('date_from', [$dateFrom, $value])
                ->orWhereBetween('date_to', [$dateFrom, $value])
                ->orWhere(function ($query) use ($dateFrom, $value) {
                  $query->where('date_from', '<=', $dateFrom)
                    ->where('date_to', '>=', $value);
                });
            })->exists();

          if ($existing) {
            $fail('The selected date range overlaps with an existing entry for this serving type.');
          }
        },
      ],
    ];

    return $this->input('serving') == "Tiffin" ? array_merge($commonRules, $tiffinRules) : $commonRules;
  }

  protected function checkDateOverlap($attribute, $value, $fail)
  {
    $tiffinSizeId = $this->input("items." . filter_var($attribute, FILTER_SANITIZE_NUMBER_INT) . ".tiffin_size_id");
    $dateTo = $this->input("items." . filter_var($attribute, FILTER_SANITIZE_NUMBER_INT) . ".date_to");

    // Get the ID of the current ServingQuantityTiffin being updated
    $currentId = $this->input("items." . filter_var($attribute, FILTER_SANITIZE_NUMBER_INT) . ".id");

    if ($dateTo && $tiffinSizeId) {
      $existing = ServingQuantityTiffin::where('tiffin_size_id', $tiffinSizeId)
        ->where('id', '<>', $currentId) // Ignore the current record being updated
        ->where(function ($query) use ($value, $dateTo) {
          $query->whereBetween('date_from', [$value, $dateTo])
            ->orWhereBetween('date_to', [$value, $dateTo])
            ->orWhere(function ($query) use ($value, $dateTo) {
              $query->where('date_from', '<=', $value)
                ->where('date_to', '>=', $dateTo);
            });
        })->exists();

      if ($existing) {
        $fail("The selected date range overlaps with an existing entry for this serving type.");
      }
    }
  }
}
