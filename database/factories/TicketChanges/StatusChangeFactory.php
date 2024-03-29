<?php

namespace Database\Factories\TicketChanges;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketChanges\StatusChange>
 */
class StatusChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // status-id
            'old_status_id' => 1,
            'new_status_id' => 2,
        ];
    }
}
