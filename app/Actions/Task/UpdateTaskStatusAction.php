<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class UpdateTaskStatusAction
{
    use ClearsStatsCache;

    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId, TaskStatus $status): Task
    {
        $task = $this->repository->update($taskId, ['status' => $status->value]);
        $this->clearStatsCache();
        return $task;
    }
}
