<?php

namespace App\Http\Requests\VendorContactPerson;

use Illuminate\Foundation\Http\FormRequest;

class ContactPersonRequest extends FormRequest
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
        $contactPersonId = $this->route('vendor_contact_person');
        return [
            'vendor_id' => 'required|integer|exists:vendors,id',
            'email' => 'nullable|email|unique:vendor_contact_persons,email,' . $contactPersonId,
            'name' => 'required|string|max:255',
            'contact_number' => ['required', 'regex:/^\+\d{1,3}\d{6,14}$/'],
            'office_number' => ['nullable', 'regex:/^\+\d{1,3}\d{6,14}$/'],
            'primary' => 'nullable|in:0,1',
        ];
    }
}
