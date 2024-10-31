<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to view user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to create user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to update user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to archived user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to restore user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return in_array('user-accounts', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to force delete user. Please contact the admin if you have any concerns.');
    }
}
