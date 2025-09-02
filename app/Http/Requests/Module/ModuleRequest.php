<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
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
        $moduleId = $this->route('module');
        $parentId = $this->input('parent_id');
        return [
            'parent_id' => 'nullable|integer|exists:modules,id',
            'name' => 'required|string|unique:modules,name,' . $moduleId,
            'icon' => 'nullable|string',
            'display_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('modules', 'display_order')
                ->where(function ($query) use ($parentId) {
                    return $query->where('parent_id', $parentId);
                })
                ->ignore($moduleId),
            ],
        ];
    }
}
