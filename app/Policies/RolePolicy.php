<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Users with "manage users" can manage roles, except the Super Admin role,
 * which only a Super Admin (via Gate::before) can change.
 */
class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('manage users');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->checkPermissionTo('manage users') && $role->name !== 'Super Admin';
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }
}
