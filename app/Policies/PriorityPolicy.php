<?php

namespace App\Policies;

use App\Models\Priority;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PriorityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user, Project $project): Response|bool
    {
        return $user->isAdded($project);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Priority $priority
     * @return Response|bool
     */
    public function view(User $user, Priority $priority): Response|bool
    {
        return $user->isAdded($priority->project);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Project $project): Response|bool
    {
        return $user->isAdmin($project);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Priority $priority
     * @return Response|bool
     */
    public function update(User $user, Priority $priority): Response|bool
    {
        return $user->isAdmin($priority->project);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Priority $priority
     * @return Response|bool
     */
    public function delete(User $user, Priority $priority): Response|bool
    {
        return $user->isAdmin($priority->project);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Priority $priority
     * @return Response|bool
     */
    public function restore(User $user, Priority $priority): Response|bool
    {
        return $user->isAdmin($priority->project);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Priority $priority
     * @return Response|bool
     */
    public function forceDelete(User $user, Priority $priority): Response|bool
    {
        return $user->isAdmin($priority->project);
    }
}
