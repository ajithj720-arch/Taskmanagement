<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Data\TaskData;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateTaskAction
{
    use ClearsStatsCache;

    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId, TaskData $data): Task
    {
        return DB::transaction(function () use ($taskId, $data): Task {
            $task = $this->repository->update($taskId, $data->toArray());
            $this->clearStatsCache();
            return $task;
        });
    }
}
