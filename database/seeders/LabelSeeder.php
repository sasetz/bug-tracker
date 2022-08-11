<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\Project;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StatusSeeder::class);
        Label::factory()
            ->count(5)
            ->for(Project::factory())
            ->create();
    }
}
