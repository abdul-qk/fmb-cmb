<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|max:255',
            'role' => 'required|exists:roles,name',
            'place_id' => 'nullable|numeric',
        ];
    }

    public function passedValidation()
    {
        $this->merge([
            'role' => Str::lower($this->role),
            'email' => Str::lower($this->email),
        ]);
    }
}
