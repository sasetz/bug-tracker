<?php

namespace App\Events;

use App\Models\TitleChange;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TitleChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public TitleChange $titleChange;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TitleChange $titleChange)
    {
        $this->titleChange = $titleChange;
    }
}
