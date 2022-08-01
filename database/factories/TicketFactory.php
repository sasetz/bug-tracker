<?php

namespace Database\Factories;

use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->sentence(),
            'number' => fake()->numberBetween(0, 100),
            'author_id' => User::factory(),
            'project_id' => Project::factory(),
            // status-id
            'status_id' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Ticket $ticket) {
            // if the user is not in the project, add the account to it
            if(!$ticket->project->users->contains($ticket->author)) {
                $ticket->project->users()->attach($ticket->author);
            }
            
            // if no priority is specified, a new from the same project is created
            if (is_null($ticket->priority)) {
                $ticket->priority()
                    ->associate(Priority::factory()->for($ticket->project, 'project')->create());
            }
        })->afterCreating(function (Ticket $ticket) {
            //
        });
    }
}
