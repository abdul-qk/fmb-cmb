<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $roleId = $this->route('role');
        return [
            'name' => 'required|string|unique:roles,name,' . $roleId,
            'modules' => 'required|array',
            'modules.*' => 'required|array',
            'modules.*.*' => [
                'required',
                'integer',
                Rule::exists('permissions', 'id') // Validates that the permission ID exists in the permissions table
            ],
        ];
    }

    public function messages()
    {
        return [
            'modules.required' => 'You must select at least one permission for any module.',
            'modules.*.required' => 'You must select at least one permission for this module.',
            'modules.*.*.exists' => 'Invalid permission selected.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => Str::slug($this->name, '-'),
            ]);
        }
    }
}
