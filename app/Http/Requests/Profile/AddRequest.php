<?php

namespace App\Http\Requests\Profile;

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
      // Personal Details
      'user_id' => 'required|integer',
      'country_id' => 'nullable|string|max:255',
      'city_id' => 'nullable|string|max:255',
      'complete_address' => 'nullable|string|max:500',
      'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional photo, image files only
      'national_identity' => 'nullable|numeric|digits_between:8,15',
      'upload_national_identity.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Optional document upload

      // Contact Details
      'email' => 'nullable|array|min:1', // At least one email is nullable
      'email.*' => 'nullable|email|max:255',
      // 'contacts' => 'nullable|array|min:1',
      // 'contacts.*.contact_types.' => ['nullable', Rule::in(['mobile', 'home'])],
      // 'contacts.*.contact_numbers.*' => 'nullable|numeric',
      'contacts' => 'nullable|array|min:1',
      'contacts.*.contact_types' => ['nullable', Rule::in(['mobile', 'home'])],
      'contacts.*.contact_numbers' => 'nullable|numeric',

      // Work Details
      'working_designation' => 'nullable|string|max:255',
      'responsibilities' => 'nullable|string|max:1000',

      // Education Details
      'education_id' => 'nullable|string|max:255',
      'status' => ['nullable', Rule::in(['completed', 'in_progress', 'just_started', 'about_to_start'])],
      'start_year' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
      'end_year' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),

      // Past Experience
      'experiences' => 'nullable|array|min:1',
      'experiences.*.company' => 'nullable|string|max:255',
      'experiences.*.years' => 'nullable|integer|min:0',
      'experiences.*.designation' => 'nullable|string|max:255',

      // Upload Misc Documents
      'misc_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',

      // Medical Information
      'disease' => 'nullable|string|max:255',
      'treatment' => 'nullable|string|max:500',
      'no_of_years' => 'nullable|integer|min:0',
      'medical_documents.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
    ];
  }
}
