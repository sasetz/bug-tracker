<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\Update;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UpdatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Ticket $ticket)
    {
        return $user->isAdded($ticket->project);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Update $update)
    {
        return $user->isAdded($update->ticket->project);
    }

    /**
     * Updates are created automatically, no one can create them.
     * Later there might be some admins-only logic.
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
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Update $update)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Update $update)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Update $update)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Update  $update
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Update $update)
    {
        return false;
    }
}
