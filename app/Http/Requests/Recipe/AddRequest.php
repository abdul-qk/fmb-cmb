<?php

namespace App\Http\Requests\Recipe;

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
    $recipeId = $this->route('recipe');
    return [
      'dish_id' => [
        'required',
        'numeric',
      ],
      'serving_item' => [
        'required',
        'string',
      ],
      'serving' => [
        'required',
        'numeric',
      ],
      'place_id' => [
        'required',
        'numeric',
      ],
      'chef' => [
        'required',
        'string',
        Rule::unique('recipes')
          ->where(function ($query) {
            return $query->where('dish_id', $this->input('dish_id'))
                         ->where('serving', $this->input('serving'))
                         ->where('place_id', $this->input('place_id'))
                         ->where('chef', $this->input('chef'));
          })->ignore($recipeId)
      ],
    ];
  }

  /**
   * Custom validation messages for the request.
   */
  public function messages(): array
  {
    return [
      'chef.unique' => 'A recipe with this combination of dish, serving, place, and chef already exists.',
    ];
  }
}
