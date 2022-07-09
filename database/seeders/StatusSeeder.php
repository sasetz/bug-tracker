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
        Status::firstOrCreate([
            'name' => 'Open',
        ]);

        Status::firstOrCreate([
            'name' => 'Closed',
        ]);
        
        Status::firstOrCreate([
            'name' => 'Ignored',
        ]);
    }
}
