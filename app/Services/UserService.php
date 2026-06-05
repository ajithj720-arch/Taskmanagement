<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function listForAssignment(): Collection
    {
        return User::query()->select('id', 'name')->orderBy('name')->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }

    public function update(User $user, array $data): User
    {
        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
