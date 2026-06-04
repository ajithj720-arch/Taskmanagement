<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly TaskService $taskService) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->list($request->only(['search', 'status', 'priority', 'assigned_to']));

        return response()->json(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->store($request->validated(), $request->user()->id);

        return response()->json(new TaskResource($task), 201);
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $this->authorize('updateStatus', $task);

        $request->validate(['status' => 'required|in:pending,in_progress,completed']);

        $task = $this->taskService->updateStatus($task->id, $request->status);

        return response()->json(new TaskResource($task));
    }

    public function aiSummary(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task = $this->taskService->refreshAISummary($task->id);

        return response()->json([
            'ai_summary' => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ]);
    }
}
