<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class InviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id'       => User::factory(),
            'receiver_id'   => User::factory(),
            'project_id'    => Project::factory(),
            'accepted'      => null,
        ];
    }
}
