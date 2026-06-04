<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Data\TaskData;
use App\Jobs\GenerateAISummary;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CreateTaskAction
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {}

    public function execute(TaskData $data, int $createdBy): Task
    {
        return DB::transaction(function () use ($data, $createdBy): Task {
            $task = $this->repository->create([
                ...$data->toArray(),
                'created_by' => $createdBy,
            ]);

            dispatch(new GenerateAISummary($task));

            Cache::forget('task.stats');

            return $task;
        });
    }
}
