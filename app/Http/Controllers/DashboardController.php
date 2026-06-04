<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'stats'       => $this->taskService->stats(),
            'recentTasks' => $this->taskService->recentForUser($user->id, $user->isAdmin()),
        ]);
    }
}
