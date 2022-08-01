<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // seed statuses
        $this->seed(StatusSeeder::class);
    }
    
    /**
     * Test project creation process.
     *
     * @return void
     */
    public function test_create_project(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        
        $response = $this->post('/projects', [
            'name' => 'A sample project',
            'description' => fake()->text(),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'name' => 'A sample project',
        ]);
    }

    /**
     * Test destroying the project.
     * 
     * @return void
     */
    public function test_delete_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();
        $project_data = $project->toArray();
        
        Sanctum::actingAs($owner);
        $response = $this->withoutMiddleware('password.confirmed')->delete('/projects/' . $project->id);
        
        $response->assertOk();
        $this->assertDatabaseMissing('projects', $project_data);
    }

    public function test_make_user_admin()
    {
        $project = Project::factory()->for(User::factory(), 'owner')->create();
        $user = User::factory()->create();
        $project->users()->attach($user);
        Sanctum::actingAs($project->owner, ['*']);
        
        $response = $this->post('/projects/' . $project->id . '/users/' . $user->id . '/admin');
        $response->assertOk();
        $project->refresh();
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'is_admin' => 1,
        ]);
    }

    /**
     * Test helper functions for user.
     * 
     * @return void
     */
    public function test_user_is_owner(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $this->assertTrue($user->isOwner($project));
        $this->assertTrue($user->isAdmin($project));
        $this->assertTrue($user->isAdded($project));
    }

    /**
     * Test helper functions for user.
     *
     * @return void
     */
    public function test_user_is_member(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for(User::factory(), 'owner')->create();
        $project->users()->attach($user);

        $this->assertFalse($user->isOwner($project));
        $this->assertFalse($user->isAdmin($project));
        $this->assertTrue($user->isAdded($project));
    }

    /**
     * Test helper functions for user.
     *
     * @return void
     */
    public function test_user_is_admin(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for(User::factory(), 'owner')->create();
        $project->users()->attach($user);
        $project->users->find($user)->pivot->is_admin = 1;
        $project->users->find($user)->pivot->save();

        $this->assertFalse($user->isOwner($project));
        $this->assertTrue($user->isAdmin($project));
        $this->assertTrue($user->isAdded($project));
    }

    /**
     * Test helper functions for user.
     *
     * @return void
     */
    public function test_user_is_foreign(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for(User::factory(), 'owner')->create();

        $this->assertFalse($user->isOwner($project));
        $this->assertFalse($user->isAdmin($project));
        $this->assertFalse($user->isAdded($project));
    }
}
