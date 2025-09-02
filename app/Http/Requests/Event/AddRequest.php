<?php

namespace App\Http\Requests\Event;

use App\Rules\EventDateTime;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string',
           'date' => [
            'required',
            'date_format:Y-m-d',
             new EventDateTime($this->start, $this->end, $this->date, false, null, $this->place_id), // Pass the event date
            ],
            'place_id' => 'required|numeric',
            'start' => 'required|string',
            'end' => 'required|string',
            'event_hours' => 'required|string',
            'meal' => 'required|string',
            'serving' => 'required|string',
            'serving_persons' => 'required|numeric',
            'no_of_thaal' => 'nullable|numeric',
            'description' => 'nullable|string',
            'host_its_no' => 'nullable|numeric',
            'host_sabeel_no' => 'nullable|numeric',
            'host_name' => 'nullable|string',
            'host_menu' => 'nullable|string',
        ];
    }
}
