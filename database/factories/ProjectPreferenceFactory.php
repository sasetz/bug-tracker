<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ProjectPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'project_id' => Project::factory()->create(),
            'name' => fake()->slug(),
            'value' => fake()->slug(),
        ];
    }
}
