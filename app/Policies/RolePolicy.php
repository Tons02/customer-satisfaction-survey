<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to view roles. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): Response
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to create user. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to update role. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to archived role. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to restore role. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): Response
    {
        return in_array('role-management', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to force delete role. Please contact the admin if you have any concerns.');
    }
}
