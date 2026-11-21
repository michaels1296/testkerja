<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('couriers', 'phone')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('couriers', 'email')],
            'level' => ['required', 'integer', 'between:1,5'],
            'registered_at' => ['sometimes', 'date'],
        ];
    }
}
