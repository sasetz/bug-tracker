<?php

namespace Tests\Feature;

use App\Models\Invite;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\InviteSeeder;
use Database\Seeders\ProjectSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InviteTest extends TestCase
{
    public function test_create()
    {
        $this->seed(ProjectSeeder::class);

        $project = Project::all()->first();
        $owner = $project->owner;
        $user = User::factory()->create();

        Sanctum::actingAs($owner, ['*']);

        $response = $this->post('/invites', [
            'receiver_id' => $user->id,
            'project_id' => $project->id,
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
        $this->seed(InviteSeeder::class);
        
        $invite = Invite::where('accepted', null)->first();
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
        $this->seed(InviteSeeder::class);

        $invite = Invite::where('accepted', null)->first();
        $user = User::whereHas('receivedInvites', function (Builder $query) use ($invite) {
            $query->where('id', '!=', $invite->id);
        })->first();
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
        $this->seed(InviteSeeder::class);

        $invite = Invite::where('accepted', null)->first();
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
        $this->seed(InviteSeeder::class);

        $invite = Invite::where('accepted', null)->first();
        $user = User::whereHas('receivedInvites', function (Builder $query) use ($invite) {
            $query->where('id', '!=', $invite->id);
        })->first();
        Sanctum::actingAs($user, ['*']);

        $response = $this->patch('/invites/' . $invite->id . '/reject');

        $response->assertForbidden();
        $invite->refresh();
        $this->assertNull($invite->accepted);
    }
}
