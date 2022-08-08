<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabelsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $update array of records for LabelChange
     * 
     * @return void
     */
    public function __construct(public array $update) {}
}
