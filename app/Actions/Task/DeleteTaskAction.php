<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Repositories\Contracts\TaskRepositoryInterface;

class DeleteTaskAction
{
    use ClearsStatsCache;

    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId): bool
    {
        $this->clearStatsCache();
        return $this->repository->delete($taskId);
    }
}
