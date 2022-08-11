<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Label;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketChanges\StatusChange;
use App\Models\User;
use Database\Seeders\StatusSeeder;
use Illuminate\Database\Seeder;

class StatusChangeSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        return StatusChange::factory()->createOne();
    }
}
