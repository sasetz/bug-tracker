<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssigneeChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public User $assignee;
    public bool $new;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $assignee, bool $new)
    {
        $this->assignee = $assignee;
        $this->new = $new;
    }
}
