<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Status $status)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * No user can create a new status, may be later implemented as only
     * app-admins feature.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * Same as creating a status, no one can do that, only from console
     * or later as an app-admin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Status $status)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Status $status)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Status $status)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Status $status)
    {
        return false;
    }
}
