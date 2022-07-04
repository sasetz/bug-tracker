<?php

namespace App\Events;

use App\Models\Invite;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InviteSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * A new invite incoming
     *
     * @var Invite
     */
    public Invite $invite;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite->withoutRelations();
    }
}
