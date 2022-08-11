<?php

namespace Database\Factories\TicketChanges;

use App\Models\Ticket;
use App\Models\TicketChanges\AssigneeChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssigneeChange>
 */
class AssigneeChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'new' => fake()->boolean(),
        ];
    }
}
