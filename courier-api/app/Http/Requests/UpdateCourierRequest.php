<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $courierId = $this->route('courier')?->id;
        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30', Rule::unique('couriers', 'phone')->ignore($courierId)],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('couriers', 'email')->ignore($courierId)],
            'level' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'registered_at' => ['sometimes', 'date'],
        ];
    }
}
