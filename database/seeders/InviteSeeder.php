<?php

namespace Database\Seeders;

use App\Models\Invite;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class InviteSeeder extends Seeder
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
            foreach ($project->users as $user)
                Invite::factory()
                    ->for($project)
                    ->for($user, 'user')
                    ->for(User::factory(), 'receiver')
                    ->create();
        }
    }
}
