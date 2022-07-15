<?php

namespace App\Events;

use App\Models\Label;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabelChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public Label $label;
    public bool $new;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Label $label, bool $new)
    {
        $this->label = $label;
        $this->new = $new;
    }
}
