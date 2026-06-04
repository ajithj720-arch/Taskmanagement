<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\TaskData;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', Rule::enum(TaskPriority::class)],
            'status'      => ['required', Rule::enum(TaskStatus::class)],
            'due_date'    => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    public function toDto(): TaskData
    {
        return TaskData::fromArray($this->validated());
    }
}
