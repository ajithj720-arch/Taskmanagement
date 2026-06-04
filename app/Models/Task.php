<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
        'ai_summary',
        'ai_priority',
    ];

    protected $casts = [
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
        'ai_priority' => TaskPriority::class,
        'due_date' => 'date',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['priority'] ?? null, fn($q, $v) => $q->where('priority', $v))
            ->when($filters['assigned_to'] ?? null, fn($q, $v) => $q->where('assigned_to', $v));
    }
}


