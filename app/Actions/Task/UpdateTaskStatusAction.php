<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class UpdateTaskStatusAction
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId, TaskStatus $status): Task
    {
        $task = $this->repository->update($taskId, ['status' => $status->value]);
        Cache::forget('task.stats');
        return $task;
    }
}
