<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Priority;
use App\Models\Ticket;
use App\Models\TicketChanges\PriorityChange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriorityChangeSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        $old_priority = Priority::factory()->for($ticket->project, 'project')->createOne();
        $new_priority = Priority::factory()->for($ticket->project, 'project')->createOne();
        $ticket->priority()->associate($new_priority);

        return PriorityChange::factory()
            ->for($old_priority, 'oldPriority')
            ->for($new_priority, 'newPriority')
            ->createOne();
    }
}
