<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of this factory model
     * 
     * @var string
     */
    protected $model = Project::class;
    
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => fake()->sentence(3),
            'description'   => fake()->text(),
            'public'        => fake()->boolean(),
            'owner_id'       => User::factory()->create(),
        ];
    }

    /**
     * Indicate that users from the current project can add others
     * without a permission
     * 
     * @return ProjectFactory
     */
    public function publicize(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'public' => true,
            ];
        });
    }
}
