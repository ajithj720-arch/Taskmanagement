<?php

declare(strict_types=1);

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

    public function monthlyCompleted(?int $userId = null, bool $isAdmin = true): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'label' => $date->format('M'),
                'start' => $date->startOfMonth()->toDateTimeString(),
                'end'   => $date->copy()->endOfMonth()->toDateTimeString(),
            ]);
        }

        return $months->map(function ($month) use ($userId, $isAdmin) {
            $query = Task::query()
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$month['start'], $month['end']]);

            if (!$isAdmin && $userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }

            return [
                'label' => $month['label'],
                'count' => $query->count(),
            ];
        })->toArray();
    }

    public function stats(?int $userId = null, bool $isAdmin = true): array
    {
        $cacheKey = $isAdmin ? 'task.stats.admin' : "task.stats.user.{$userId}";

        return Cache::remember($cacheKey, 60, function () use ($userId, $isAdmin) {
            $query = Task::query()
                ->when(!$isAdmin && $userId, fn($q) => $q->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                }));

            return [
                'total' => (clone $query)->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
                'high_priority' => (clone $query)->where('priority', 'high')->count(),
            ];
        });
    }
}


