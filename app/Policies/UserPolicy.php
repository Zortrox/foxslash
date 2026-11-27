<?php

namespace App\Policies;

use App\Models\User;

/**
 * Users with "manage users" can manage accounts in Filament, except Super
 * Admins, whom only another Super Admin (via Gate::before) can touch.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->checkPermissionTo('manage users') && ! $model->hasRole('Super Admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $this->update($user, $model) && $user->isNot($model);
    }
}
