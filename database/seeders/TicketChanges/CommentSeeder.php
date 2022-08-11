<?php

namespace Database\Seeders\TicketChanges;

use App\Models\Ticket;
use App\Models\TicketChanges\Comment;

class CommentSeeder extends ChangeSeeder
{
    protected function makeChanges(Ticket $ticket)
    {
        return Comment::factory()->createOne();
    }
}
