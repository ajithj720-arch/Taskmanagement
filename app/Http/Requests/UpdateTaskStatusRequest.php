<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'A status is required.',
            'status.in'       => 'Status must be one of: pending, in_progress, completed.',
        ];
    }
}
