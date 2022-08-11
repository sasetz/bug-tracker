<?php

namespace Database\Factories\TicketChanges;

use App\Models\Label;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketChanges\LabelChange>
 */
class LabelChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'new' => fake()->boolean(),
        ];
    }
}
