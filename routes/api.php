<?php

use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::patch('/tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
    Route::get('/tasks/{task}/ai-summary', [TaskApiController::class, 'aiSummary']);
});
