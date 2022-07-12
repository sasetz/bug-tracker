<?php

namespace Database\Seeders;

use App\Models\Priority;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Priority::factory()
            ->count(7)
            ->for(Project::factory())
            ->create();
    }
}
