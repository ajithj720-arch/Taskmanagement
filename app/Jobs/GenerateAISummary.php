<?php

namespace App\Jobs;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAISummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public readonly Task $task) {}

    public function handle(AIService $aiService, TaskRepositoryInterface $repo): void
    {
        try {
            $aiData = $aiService->generateSummary($this->task);
            $repo->update($this->task->id, $aiData);
        } catch (\Throwable $e) {
            Log::error('GenerateAISummary job failed', [
                'task_id' => $this->task->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
