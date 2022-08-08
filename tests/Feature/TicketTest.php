<?php

namespace Tests\Feature;

use App\Events\AssigneeChanged;
use App\Events\CommentPosted;
use App\Events\LabelsChanged;
use App\Events\PriorityChanged;
use App\Events\StatusChanged;
use App\Events\TitleChanged;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\StatusSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // seed statuses
        $this->seed(StatusSeeder::class);
    }

    /**
     * Test for issuing a ticket
     *
     * @return void
     */
    public function test_user_can_create_tickets(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $label = Label::factory()->for($project, 'project')->create();
        $priority = Priority::factory()->for($project, 'project')->create();
        Sanctum::actingAs($user, ['*']);

        $data = [
            'name' => fake()->sentence(),
            'message' => fake()->text(),
            'label_ids' => [$label->id],
            'assignee_ids' => [$user->id],
            'priority_id' => $priority->id,
        ];
        $response = $this->post('/projects/' . $project->id . '/tickets', $data);

        $response
            ->assertOk();
        $this->assertDatabaseHas('tickets', [
            'name' => $data['name'],
            'author_id' => $user->id,
            'project_id' => $project->id,
            'priority_id' => $priority->id,
            // status-id
            'status_id' => 1,
        ]);
        Event::assertDispatched(CommentPosted::class);
    }

    public function test_tickets_can_be_searched()
    {
        $quantity = 10;

        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->users()->attach($user);
        $tickets = Ticket::factory()->for($project, 'project')->count($quantity)->create();
        $tickets->each(function ($item, $key) use ($project) {
            $project->users()->attach($item->author);
        });
        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/projects/' . $project->id . '/tickets');

        $response->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->count('data', $quantity)
                ->hasAll(['data', 'links', 'meta'])
                ->etc());
    }

    public function test_foreign_user_cannot_create_ticket()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $label = Label::factory()->for($project, 'project')->create();
        $priority = Priority::factory()->for($project, 'project')->create();
        Sanctum::actingAs($user, ['*']);

        $data = [
            'name' => fake()->sentence(),
            'message' => fake()->text(),
            'label_ids' => [$label->id],
            'priority_id' => $priority->id,
        ];
        $response = $this->post('/projects/' . $project->id . '/tickets', $data);

        $response
            ->assertForbidden();
        $this->assertDatabaseMissing('tickets', [
            'name' => $data['name'],
            'author_id' => $user->id,
            'project_id' => $project->id,
            'priority_id' => $priority->id,
            // status-id
            'status_id' => 1,
        ]);
    }

    public function test_view_ticket()
    {
        $ticket = Ticket::factory()->create();
        Sanctum::actingAs($ticket->author, ['*']);

        $response = $this->get('/tickets/' . $ticket->id);

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('name', $ticket->name)
                ->where('author.id', $ticket->author->id)
                ->etc());
    }

    public function test_delete_ticket()
    {
        $ticket = Ticket::factory()->create();
        Sanctum::actingAs($ticket->author, ['*']);
        $ticket_data = $ticket->attributesToArray();
        
        $response = $this->delete('/tickets/' . $ticket->id);
        
        $response->assertOk();
        $this->assertDatabaseMissing('tickets', $ticket_data);
    }

    public function test_add_assignee_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->project->users()->attach($user);
        Sanctum::actingAs($ticket->author, ['*']);
        
        $response = $this->patch('/tickets/' . $ticket->id, [
            'assignee_ids' => [
                $user->id,
            ],
        ]);
        
        $response->assertOk();
        $this->assertDatabaseHas('ticket_assignees', [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);
    }

    public function test_remove_assignee_from_ticket()
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->project->users()->attach($user);
        $ticket->assignees()->attach($user);
        Sanctum::actingAs($ticket->author, ['*']);

        $response = $this->patch('/tickets/' . $ticket->id, [
            'assignee_ids' => [],
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('ticket_assignees', [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);
    }

    public function test_user_can_subscribe_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->project->users()->attach($user);
        Sanctum::actingAs($user, ['*']);
        
        $response = $this->patch('/tickets/' . $ticket->id . '/subscribe');
        
        $response->assertOk();
        $this->assertDatabaseHas('ticket_subscriptions', [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);
    }

    public function test_user_can_unsubscribe_from_ticket()
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->project->users()->attach($user);
        $ticket->subscribers()->attach($user);
        Sanctum::actingAs($user, ['*']);

        $response = $this->patch('/tickets/' . $ticket->id . '/unsubscribe');

        $response->assertOk();
        $this->assertDatabaseMissing('ticket_subscriptions', [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);
    }

    /**
     * @return void
     */
    public function test_changes_get_recorded(): void
    {
        Event::fake();
        $ticket = Ticket::factory()->create();
        $priority = Priority::factory()->for($ticket->project, 'project')->create();
        $labels_init = Label::factory()->for($ticket->project, 'project')->count(3)->create();
        $labels_new = Label::factory()->for($ticket->project, 'project')->count(3)->create();
        $labels_new[] = $labels_init[0];
        $labels_init->each(function ($item, $key) use (&$ticket) {
            $ticket->labels()->attach($item);
        });
        $assignees = User::factory()->hasAttached($ticket->project)->count(3)->create();
        Sanctum::actingAs($ticket->author, ['*']);
        
        $response = $this->withoutMiddleware(['verified'])->patch('/tickets/' . $ticket->id, [
            'name' => fake()->sentence(3),
            // status-id
            'status_id' => 2,
            'priority_id' => $priority->id,
            'label_ids' => $labels_new->modelKeys(),
            'assignee_ids' => $assignees->modelKeys(),
        ]);
        
        $response->assertOk();
        Event::assertDispatched(TitleChanged::class);
        Event::assertDispatched(StatusChanged::class);
        Event::assertDispatched(PriorityChanged::class);
        Event::assertDispatched(LabelsChanged::class);
        Event::assertDispatched(AssigneeChanged::class);
    }
}
