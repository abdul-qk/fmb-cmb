<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
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
        $vendorId = $this->route('vendor');
        $contactId = $this->input('contact_id');
        $bankId = $this->input('bank_id');
        return [
            'name' => 'required|string',
            'email' => 'nullable|email|unique:vendors,email,' . $vendorId,
            'city_id' => 'nullable|integer|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'contact_number' => [
              'nullable',
              'regex:/^\+\d{1,3}\d{6,14}$/',
              Rule::unique('vendor_contact_persons', 'contact_number')->ignore($contactId)
            ],
            'office_number' => ['nullable', 'regex:/^\+\d{1,3}\d{6,14}$/'],
            'ntn' => 'nullable|string',
            'bank' => 'nullable|string',
            'bank_title' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'account_no' =>  [
              'nullable',
              'string',
              'max:255',
              Rule::unique('vendor_banks', 'account_no')->ignore($bankId)
            ],
            'items' => 'nullable|array',
            'items.*' => 'integer|exists:items,id', 
            'contact_id' => 'nullable|string',
            'bank_id' => 'nullable|string',
        ];
    }
}
