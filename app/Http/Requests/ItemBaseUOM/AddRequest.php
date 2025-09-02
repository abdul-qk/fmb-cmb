<?php

namespace App\Http\Requests\ItemBaseUOM;

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
    $result = $this->route('item_base_uom');
    // return [
    //   'item_id' => [
    //     'required',
    //     'numeric',
    //     'unique:item_base_uoms,item_id,' . $this->route('item_base_uom') . ',id,base_uom,' . $this->base_uom,
    //     // Rule::unique('item_base_uoms')
    //     //   ->where(function ($query) {
    //     //     return $query->where('base_uom', $this->base_uom);
    //     //   })
    //     //   ->ignore($result->id ?? null),
    //   ],
    //   'base_uom' => [
    //     'required',
    //     'numeric',
    //   ],
    // ];
    return [
      'item_id' => 'required|string|unique:item_base_uoms,item_id,'. $this->route('item_base_uom'),
      'unit_measure_id' => [
        'required',
        'numeric',
      ],
  ];
  }
}
