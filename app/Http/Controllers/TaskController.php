<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly UserService $userService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->only(['search', 'status', 'priority', 'assigned_to']);

        // Non-admin users only see tasks assigned to them
        if (!$request->user()->isAdmin()) {
            $filters['assigned_to'] = $request->user()->id;
        }

        $tasks = $this->taskService->list($filters);

        return view('tasks.index', [
            'tasks'   => $tasks,
            'filters' => $filters,
            'users'   => $this->userService->listForAssignment(),
            'stats'   => $this->taskService->stats(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'users' => $this->userService->listForAssignment(),
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->store($request->validated(), $request->user()->id);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task, Request $request)
    {
        $this->authorize('view', $task);

        if ($request->has('refresh_ai') || $request->has('do_refresh')) {
            $task = $this->taskService->refreshAISummary($task->id);
        } else {
            $task->load(['assignee', 'creator']);
        }

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task'  => $task->load(['assignee', 'creator']),
            'users' => $this->userService->listForAssignment(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->update($task->id, $request->validated());

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskService->delete($task->id);

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('updateStatus', $task);
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);
        $this->taskService->updateStatus($task->id, $request->status);

        return back()->with('success', 'Status updated.');
    }
}
