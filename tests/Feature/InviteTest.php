<?php

namespace Tests\Feature;

use App\Models\Invite;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\InviteSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InviteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // seed statuses
        $this->seed(StatusSeeder::class);
    }
    
    public function test_create()
    {
        $project = Project::factory()->create();
        $owner = $project->owner;
        $project->users()->attach($project->owner);
        $user = User::factory()->create();

        Sanctum::actingAs($owner, ['*']);

        $response = $this->post('/projects/'. $project->id .'/invites', [
            'receiver_id' => $user->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('invites', [
            'receiver_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Test if an invitation can be accepted
     * 
     * @return void
     */
    public function test_accept(): void
    {
        $invite = Invite::factory()->create();
        $user = $invite->receiver;
        Sanctum::actingAs($user, ['*']);
        
        $response = $this->patch('/invites/' . $invite->id . '/accept');
        
        $response->assertOk();
        $invite->refresh();
        $this->assertEquals(1, $invite->accepted);
    }

    /**
     * Test if someone else's invitation cannot be accepted
     *
     * @return void
     */
    public function test_accept_foreign_forbidden(): void
    {
        $invite = Invite::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->patch('/invites/' . $invite->id . '/accept');

        $response->assertForbidden();
        $invite->refresh();
        $this->assertNull($invite->accepted);
    }

    /**
     * Test if an invitation can be rejected
     *
     * @return void
     */
    public function test_reject(): void
    {
        $invite = Invite::factory()->create();
        $user = $invite->receiver;
        Sanctum::actingAs($user, ['*']);

        $response = $this->patch('/invites/' . $invite->id . '/reject');

        $response->assertOk();
        $invite->refresh();
        $this->assertEquals(0, $invite->accepted);
    }

    /**
     * Test if someone else's invitation cannot be rejected
     *
     * @return void
     */
    public function test_reject_foreign_forbidden(): void
    {
        $invite = Invite::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->patch('/invites/' . $invite->id . '/reject');

        $response->assertForbidden();
        $invite->refresh();
        $this->assertNull($invite->accepted);
    }

    /**
     * Test that only members of the project can add other members.
     * 
     * @return void
     */
    public function test_create_to_foreign_project(): void
    {
        $project = Project::factory()->create();
        $project->users()->attach($project->owner);
        
        $sender = User::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($sender, ['*']);

        $response = $this->post('/projects/'. $project->id .'/invites', [
            'receiver_id' => $user->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('invites', [
            'user_id' => $sender->id,
            'receiver_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Test that any non-admin user can add other users for public project.
     * 
     * @return void
     */
    public function test_create_to_public_project(): void
    {
        $project = Project::factory()->public()->create();
        $project->users()->attach($project->owner);

        $sender = User::factory()->create();
        $user = User::factory()->create();
        
        $project->users()->attach($sender);

        Sanctum::actingAs($sender, ['*']);

        $response = $this->post('/projects/'. $project->id .'/invites', [
            'receiver_id' => $user->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('invites', [
            'user_id' => $sender->id,
            'receiver_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Test that only admins can add other users to a private project.
     * 
     * @return void
     */
    public function test_create_to_private_project(): void
    {
        $project = Project::factory()->private()->create();
        $project->users()->attach($project->owner);

        $sender = User::factory()->create();
        $user = User::factory()->create();

        $project->users()->attach($sender);

        Sanctum::actingAs($sender, ['*']);

        $response = $this->post('/projects/'. $project->id .'/invites', [
            'receiver_id' => $user->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('invites', [
            'user_id' => $sender->id,
            'receiver_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_view_all_invites()
    {
        $user = User::factory()->create();
        $invites = Invite::factory()->for($user, 'receiver')->count(3)->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->get('/invites');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data')
                ->count('data', 3)
                ->etc()
            );
    }

    public function test_view_single_invite()
    {
        $user = User::factory()->create();
        $invites = Invite::factory()->for($user, 'receiver')->count(3)->create();
        $invite = $invites[0];

        Sanctum::actingAs($user, ['*']);
        $response = $this->get('/invites/' . $invite->id);
        
        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'sender', 'receiver', 'project'])
                ->where('id', $invite->id)
                ->where('accepted', null)
                ->etc()
            );
    }
}
