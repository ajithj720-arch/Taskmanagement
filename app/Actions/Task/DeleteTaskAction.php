<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DeleteTaskAction
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId): bool
    {
        Cache::forget('task.stats');
        return $this->repository->delete($taskId);
    }
}
