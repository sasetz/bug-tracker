<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function view(User $user, Project $project): Response|bool
    {
        return $user->isAdded($project);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function update(User $user, Project $project): Response|bool
    {
        return $user->isAdmin($project);
    }

    /**
     * Determine whether the user can delete the model.
     * Only owners can remove projects
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function delete(User $user, Project $project): Response|bool
    {
        return $user->isOwner($project);
    }

    /**
     * Determine whether the user can add a new admin
     * to the project
     * 
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function makeAdmin(User $user, Project $project): bool
    {
        return $user->isOwner($project);
    }

    /**
     * Determine if the user can create an invitation to the project.
     * 
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function create_invite(User $user, Project $project): bool
    {
        return $user->isAdmin($project) || $user->isAdded($project) && $project->public == 1;
    }
}
