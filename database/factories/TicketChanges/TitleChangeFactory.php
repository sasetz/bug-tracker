<?php

namespace Database\Factories\TicketChanges;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketChanges\TitleChange>
 */
class TitleChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'old' => fake()->sentence(),
            'new' => fake()->sentence(),
        ];
    }

    public function makeTicketRelations(mixed $change, Ticket $ticket): void
    {
        $ticket->name = $change->new;
        $ticket->save();
    }
}
