<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Status::where('name', 'Open')->first())
            Status::create([
                'name' => 'Open',
            ]);

        if (!Status::where('name', 'Closed')->first())
            Status::create([
                'name' => 'Closed',
            ]);

        if (!Status::where('name', 'Ignored')->first())
            Status::create([
                'name' => 'Ignored',
            ]);
    }
}
