<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\RefreshAISummaryAction;
use App\Actions\Task\UpdateTaskStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->list(
            $request->only(['search', 'status', 'priority', 'assigned_to'])
        );

        return response()->json(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request, CreateTaskAction $action): JsonResponse
    {
        $task = $action->execute($request->toDto(), $request->user()->id);

        return response()->json(new TaskResource($task), 201);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task, UpdateTaskStatusAction $action): JsonResponse
    {
        $task = $action->execute($task->id, $request->status());

        return response()->json(new TaskResource($task));
    }

    public function aiSummary(Task $task, RefreshAISummaryAction $action): JsonResponse
    {
        $this->authorize('view', $task);

        $task = $action->execute($task->id);

        return response()->json([
            'ai_summary'  => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ]);
    }
}
