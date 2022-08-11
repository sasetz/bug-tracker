<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Ticket;
use App\Models\TicketChanges\AssigneeChange;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssigneeChangeSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        $assignee = User::factory()->for($ticket->project, 'project')->createOne();
        $change = AssigneeChange::factory()->for($assignee, 'assignee')->createOne();
        if($change->new) {
            $ticket->assignees()->attach($assignee);
        }
        
        return $change;
    }
}
