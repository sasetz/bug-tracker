<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Ticket;
use App\Models\Update;
use Illuminate\Database\Seeder;

abstract class ChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticket = Ticket::factory()->createOne();
        
        $change = $this->makeChanges($ticket);
        
        Update::factory()
            ->for($change, 'changeable')
            ->for($ticket->author, 'user')
            ->for($ticket, 'ticket')
            ->create();
    }
    
    abstract protected function makeChanges(Ticket $ticket);
}
