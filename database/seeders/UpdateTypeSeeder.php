<?php

namespace Database\Seeders;

use App\Models\UpdateType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!UpdateType::where('name', 'change-title')->first())
            UpdateType::create([
                'name' => 'change-title',
            ]);
        if (!UpdateType::where('name', 'change-status')->first())
            UpdateType::create([
                'name' => 'change-status',
            ]);
        if (!UpdateType::where('name', 'change-priority')->first())
            UpdateType::create([
                'name' => 'change-priority',
            ]);
        if (!UpdateType::where('name', 'comment')->first())
            UpdateType::create([
                'name' => 'comment',
            ]);
        if (!UpdateType::where('name', 'add-assignee')->first())
            UpdateType::create([
                'name' => 'add-assignee',
            ]);
        if (!UpdateType::where('name', 'remove-assignee')->first())
            UpdateType::create([
                'name' => 'remove-assignee',
            ]);
        if (!UpdateType::where('name', 'add-label')->first())
            UpdateType::create([
                'name' => 'add-label',
            ]);
        if (!UpdateType::where('name', 'remove-label')->first())
            UpdateType::create([
                'name' => 'remove-label',
            ]);
    }
}
