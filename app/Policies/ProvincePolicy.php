<?php

namespace App\Policies;

use App\Models\Province;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProvincePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Province $province): Response
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array('province', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to create provinces. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Province $province): Response
    {
        return in_array('province', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to update provinces. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Province $province): Response
    {
        return in_array('province', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to archived provinces. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Province $province): Response
    {
        return in_array('province', $user->role['access_permission'])
        ? Response::allow()
        : Response::deny('You do not have permission to restore provinces. Please contact the admin if you have any concerns.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Province $province): Response
    {
        //
    }

}
