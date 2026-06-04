<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\RefreshAISummaryAction;
use App\Actions\Task\UpdateTaskAction;
use App\Actions\Task\UpdateTaskStatusAction;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->only(['search', 'status', 'priority', 'assigned_to']);

        if (! $request->user()->isAdmin()) {
            $filters['assigned_to'] = $request->user()->id;
        }

        return view('tasks.index', [
            'tasks'   => $this->taskService->list($filters),
            'filters' => $filters,
            'users'   => $this->userService->listForAssignment(),
            'stats'   => $this->taskService->stats(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'users' => $this->userService->listForAssignment(),
        ]);
    }

    public function store(StoreTaskRequest $request, CreateTaskAction $action): RedirectResponse
    {
        $task = $action->execute($request->toDto(), $request->user()->id);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task, Request $request, RefreshAISummaryAction $action): View
    {
        $this->authorize('view', $task);

        if ($request->has('refresh_ai')) {
            $task = $action->execute($task->id);
        } else {
            $task->load(['assignee', 'creator']);
        }

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task'  => $task->load(['assignee', 'creator']),
            'users' => $this->userService->listForAssignment(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action): RedirectResponse
    {
        $action->execute($task->id, $request->toDto());

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task, DeleteTaskAction $action): RedirectResponse
    {
        $this->authorize('delete', $task);

        $action->execute($task->id);

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task, UpdateTaskStatusAction $action): RedirectResponse
    {
        $action->execute($task->id, $request->status());

        return back()->with('success', 'Status updated.');
    }
}
