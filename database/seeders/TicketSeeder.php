<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run()
    {
        $projects = Project::factory()->count(3)->has(User::factory()->count(3))->create();

        foreach ($projects as $project) {
            foreach ($project->users as $user) {
                $tickets = Ticket::factory()
                    ->count(3)
                    ->for($user, 'author')
                    ->for($project)
                    ->for(Priority::factory()->for($project))
                    ->has(Label::factory()->for($project))
                    ->create();
                
                $tickets->each(function ($ticket) use ($user) {
                    $ticket->subscribers()->attach($user);
                    $ticket->assignees()->attach($user);
                });
            }
        }
    }
}
