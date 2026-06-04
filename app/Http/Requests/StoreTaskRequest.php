<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\TaskData;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Task::class);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', Rule::enum(TaskPriority::class)],
            'status'      => ['required', Rule::enum(TaskStatus::class)],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    public function toDto(): TaskData
    {
        return TaskData::fromArray($this->validated());
    }
}
