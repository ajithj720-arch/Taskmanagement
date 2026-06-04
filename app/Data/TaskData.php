<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

readonly class TaskData
{
    public function __construct(
        public string $title,
        public TaskPriority $priority,
        public TaskStatus $status,
        public ?string $description = null,
        public ?string $due_date = null,
        public ?int $assigned_to = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            priority: TaskPriority::from($data['priority']),
            status: TaskStatus::from($data['status']),
            description: $data['description'] ?? null,
            due_date: $data['due_date'] ?? null,
            assigned_to: isset($data['assigned_to']) ? (int) $data['assigned_to'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title'       => $this->title,
            'description' => $this->description,
            'priority'    => $this->priority->value,
            'status'      => $this->status->value,
            'due_date'    => $this->due_date,
            'assigned_to' => $this->assigned_to,
        ], fn($v) => $v !== null);
    }
}
