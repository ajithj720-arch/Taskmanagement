<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\AIService;

class RefreshAISummaryAction
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
        private readonly AIService $aiService,
    ) {}

    public function execute(int $taskId): Task
    {
        $task = $this->repository->find($taskId);
        $aiData = $this->aiService->generateSummary($task);
        return $this->repository->update($taskId, $aiData);
    }
}
