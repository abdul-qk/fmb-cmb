<?php

namespace App\Http\Requests\VendorBank;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankRequest extends FormRequest
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
            'vendor_id' => 'required|integer|exists:vendors,id',       
            'ntn' => 'nullable|string|max:255',
            'bank' => 'required|string|max:255',
            'bank_title' => 'required|string|max:255',
            'bank_address' => 'required|string|max:255',
            'account_no' =>  [
              'required',
              'string',
              'max:255',
              Rule::unique('vendor_banks', 'account_no')->ignore($this->route('vendor_bank'))
            ],
            'bank_branch' => 'required|string|max:255',
            'primary' => 'nullable|in:0,1',
        ];
    }
}
