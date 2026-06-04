<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_color' => $this->priority->color(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'due_date' => $this->due_date?->toDateString(),
            'is_overdue' => $this->due_date && $this->due_date->isPast() && $this->status->value !== 'completed',
            'ai_summary' => $this->ai_summary,
            'ai_priority' => $this->ai_priority?->value,
            'assigned_to' => $this->assigned_to,
            'assignee' => $this->whenLoaded('assignee', fn() => [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ]),
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}


