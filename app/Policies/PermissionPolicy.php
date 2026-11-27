<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Users with "manage users" can manage permissions, except "manage users"
 * itself, so nobody but a Super Admin can rename or remove it.
 */
class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->checkPermissionTo('manage users') && $permission->name !== 'manage users';
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $this->update($user, $permission);
    }
}
