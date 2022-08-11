<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Label;
use App\Models\Ticket;
use App\Models\TicketChanges\LabelChange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabelChangeSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        $label = Label::factory()->for($ticket->project, 'project')->createOne();
        $change = LabelChange::factory()->for($label, 'label')->createOne();
        if($change->new) {
            $ticket->labels()->attach($label);
        }

        return $change;
    }
}
