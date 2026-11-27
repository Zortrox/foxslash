<?php

namespace App\Policies;

use App\Models\SteamGame;
use App\Models\User;

class SteamGamePolicy
{
    /**
     * Determine whether the user can view any models. Steam games are for
     * Super Admins only, who pass every gate through Gate::before.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SteamGame $steamGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SteamGame $steamGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SteamGame $steamGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SteamGame $steamGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SteamGame $steamGame): bool
    {
        return false;
    }
}
