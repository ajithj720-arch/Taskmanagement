<?php

declare(strict_types=1);

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
            'stats'            => $this->taskService->stats($user->id, $user->isAdmin()),
            'recentTasks'      => $this->taskService->recentForUser($user->id, $user->isAdmin()),
            'monthlyCompleted' => $this->taskService->monthlyCompleted($user->id, $user->isAdmin()),
        ]);
    }
}


