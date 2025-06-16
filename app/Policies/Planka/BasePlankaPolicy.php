<?php

namespace App\Policies\Planka;

use App\Models\Planka\AuthenticatableUserAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePlankaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthenticatableUserAccount $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AuthenticatableUserAccount $user, $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthenticatableUserAccount $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthenticatableUserAccount $user, $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthenticatableUserAccount $user, $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(AuthenticatableUserAccount $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AuthenticatableUserAccount $user, $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AuthenticatableUserAccount $user, $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(AuthenticatableUserAccount $user): bool
    {
        return false;
    }
}