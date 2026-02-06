<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class UserPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny($user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine if the user can view the model.
     */
    public function view($user, User $model): bool
    {
        return $user instanceof Admin || ($user instanceof User && $user->id === $model->id);
    }

    /**
     * Determine if the user can create models.
     */
    public function create($user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine if the user can update the model.
     */
    public function update($user, User $model): bool
    {
        return $user instanceof Admin || ($user instanceof User && $user->id === $model->id);
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete($user, User $model): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine if the user can toggle active status.
     */
    public function toggleActive($user): bool
    {
        return $user instanceof Admin;
    }
}