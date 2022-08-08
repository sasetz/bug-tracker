<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssigneeChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $update array of records for AssigneeChange
     * 
     * @return void
     */
    public function __construct(public array $update) {}
}
