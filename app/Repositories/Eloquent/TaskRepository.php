<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Task::query()
            ->with(['assignee', 'creator'])
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function find(int $id): Task
    {
        return Cache::remember("task.{$id}", 300, fn() =>
            Task::with(['assignee', 'creator'])->findOrFail($id)
        );
    }

    public function create(array $data): Task
    {
        $task = Task::create($data);
        Cache::forget("task.{$task->id}");
        return $task->load(['assignee', 'creator']);
    }

    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        Cache::forget("task.{$id}");
        return $task->fresh(['assignee', 'creator']);
    }

    public function delete(int $id): bool
    {
        Cache::forget("task.{$id}");
        return Task::findOrFail($id)->delete();
    }

    public function recent(int $userId, bool $isAdmin, int $limit = 5)
    {
        return Task::with(['assignee', 'creator'])
            ->when(!$isAdmin, fn($q) => $q->where(function ($q) use ($userId) {
                $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
            }))
            ->latest()
            ->take($limit)
            ->get();
    }

    public function stats(): array
    {
        return Cache::remember('task.stats', 60, function () {
            return [
                'total' => Task::count(),
                'completed' => Task::where('status', 'completed')->count(),
                'pending' => Task::where('status', 'pending')->count(),
                'in_progress' => Task::where('status', 'in_progress')->count(),
                'high_priority' => Task::where('priority', 'high')->count(),
            ];
        });
    }
}
