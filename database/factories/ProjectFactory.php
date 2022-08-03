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
            'public'        => false,
            'owner_id'       => User::factory(),
        ];
    }
    
    public function configure()
    {
        return $this->afterMaking(function (Project $project) {
        })->afterCreating(function (Project $project) {
            // the project owner should be in the project
            $project->users()->attach($project->owner);
        });
    }

    /**
     * Indicate that users from the current project can add others
     * without a permission.
     * 
     * @return ProjectFactory
     */
    public function public(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'public' => true,
            ];
        });
    }

    /**
     * Indicate that users from the current project cannot add others
     * unless they have admins rights
     *
     * @return ProjectFactory
     */
    public function private(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'public' => false,
            ];
        });
    }
}
