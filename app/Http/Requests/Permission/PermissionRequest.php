<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Module;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
        $permissionId = $this->route('permission');
        return [
            'module_id' => 'required|integer|exists:modules,id',
            'name' => [
                'required',
                'string',
                Rule::unique('permissions')
                    ->where(function ($query) {
                        return $query->where('module_id', $this->module_id);
                    })
                    ->ignore($permissionId, 'id'),
            ],
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
