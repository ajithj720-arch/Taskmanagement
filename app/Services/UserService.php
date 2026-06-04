<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function listForAssignment(): Collection
    {
        return User::select('id', 'name')->orderBy('name')->get();
    }
}
