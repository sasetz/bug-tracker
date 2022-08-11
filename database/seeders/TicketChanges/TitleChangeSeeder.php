<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Label;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketChanges\TitleChange;
use App\Models\User;
use Database\Seeders\StatusSeeder;
use Illuminate\Database\Seeder;

class TitleChangeSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        $change = TitleChange::factory()->createOne();
        
        $ticket->name = $change->new;
        $ticket->save();
        
        return $change;
    }
}
