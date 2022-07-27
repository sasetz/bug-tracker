<?php

namespace App\Policies;

use App\Models\Invite;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class InvitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Invite $invite
     * @return Response|bool
     */
    public function view(User $user, Invite $invite): Response|bool
    {
        return $invite->user === $user || $invite->receiver === $user;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function create(User $user, Project $project): Response|bool
    {
        return $user->isAdmin($project) || $user->isAdded($project) && $project->public;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Invite $invite
     * @return Response|bool
     */
    public function delete(User $user, Invite $invite): Response|bool
    {
        return $invite->user->is($user);
    }

    /**
     * Determine whether the user can accept or reject the invite.
     * 
     * @param User $user
     * @param Invite $invite
     * @return bool
     */
    public function change_status(User $user, Invite $invite): bool
    {
        return $invite->receiver->is($user) && ($invite->accepted == null);
    }
}
