<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;

    public function find(int $id): Task;

    public function create(array $data): Task;

    public function update(int $id, array $data): Task;

    public function delete(int $id): bool;

    public function stats(?int $userId = null, bool $isAdmin = true): array;

    public function recent(int $userId, bool $isAdmin, int $limit = 5);

    public function monthlyCompleted(?int $userId = null, bool $isAdmin = true): array;
}


