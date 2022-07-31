<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return Response|bool
     */
    public function view(User $user, Ticket $ticket): Response|bool
    {
        return $user->isAdded($ticket->project);
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
        return $user->isAdded($project);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return Response|bool
     */
    public function update(User $user, Ticket $ticket): Response|bool
    {
        return $user->isAdmin($ticket->project) || $ticket->author()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return Response|bool
     */
    public function delete(User $user, Ticket $ticket): Response|bool
    {
        return $user->isAdmin($ticket->project) || $ticket->author()->is($user);
    }

    /**
     * Checks if the user can subscribe to the given ticket.
     * 
     * @param User $user
     * @param Ticket $ticket
     * @return Response|bool
     */
    public function subscribe(User $user, Ticket $ticket): Response|bool
    {
        return $user->isAdded($ticket->project);
    }

    /**
     * Checks if the user can unsubscribe to the given ticket.
     * 
     * @param User $user
     * @param Ticket $ticket
     * @return Response|bool
     */
    public function unsubscribe(User $user, Ticket $ticket): Response|bool
    {
        return $ticket->subscribers->contains($user);
    }
}
