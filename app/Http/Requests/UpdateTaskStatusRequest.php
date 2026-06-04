<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskStatus;
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
            'status' => ['required', Rule::enum(TaskStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'A status is required.',
            'status.enum'     => 'Status must be one of: pending, in_progress, completed.',
        ];
    }

    public function status(): TaskStatus
    {
        return TaskStatus::from($this->validated()['status']);
    }
}
