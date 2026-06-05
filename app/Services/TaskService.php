<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $repo,
        private readonly AIService $aiService,
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->all($filters);
    }

    public function store(array $data, int $createdBy): Task
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $data['created_by'] = $createdBy;
            $task = $this->repo->create($data);

            dispatch(new \App\Jobs\GenerateAISummary($task));

            $this->clearStatsCache();
            return $task;
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            $task = $this->repo->update($id, $data);
            $this->clearStatsCache();
            return $task;
        });
    }

    public function updateStatus(int $id, string $status): Task
    {
        return $this->update($id, ['status' => $status]);
    }

    public function delete(int $id): bool
    {
        $this->clearStatsCache();
        return $this->repo->delete($id);
    }

    public function find(int $id): Task
    {
        return $this->repo->find($id);
    }

    public function stats(?int $userId = null, bool $isAdmin = true): array
    {
        return $this->repo->stats($userId, $isAdmin);
    }

    public function refreshAISummary(int $id): Task
    {
        $task = $this->repo->find($id);
        $aiData = $this->aiService->generateSummary($task);
        return $this->repo->update($id, $aiData);
    }

    public function monthlyCompleted(?int $userId = null, bool $isAdmin = true): array
    {
        return $this->repo->monthlyCompleted($userId, $isAdmin);
    }

    public function recentForUser(int $userId, bool $isAdmin, int $limit = 5)
    {
        return $this->repo->recent($userId, $isAdmin, $limit);
    }

    private function clearStatsCache(): void
    {
        Cache::forget('task.stats.admin');

        $userIds = \App\Models\User::pluck('id');
        foreach ($userIds as $id) {
            Cache::forget("task.stats.user.{$id}");
        }
    }
}


