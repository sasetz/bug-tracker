<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
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
        
        dump($response);
        
        $response->assertOk();
        $this->assertDatabaseHas('invites', [
            'receiver_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }
}
