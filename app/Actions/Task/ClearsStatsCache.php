<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

trait ClearsStatsCache
{
    private function clearStatsCache(): void
    {
        Cache::forget('task.stats.admin');

        User::pluck('id')->each(function (int $id): void {
            Cache::forget("task.stats.user.{$id}");
        });
    }
}
