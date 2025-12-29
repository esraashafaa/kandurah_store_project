<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }


    public function update(User $user, User $model): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN || $user->id === $model->id;
    }

    
    public function delete(User $user, User $model): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }

   
    public function toggleActive(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }
}