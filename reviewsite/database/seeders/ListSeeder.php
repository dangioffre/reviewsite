<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListModel;

class ListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 lists with as much info as possible
        ListModel::factory()
            ->count(15)
            ->create();

        $this->command->info('Created 10 lists with max info!');
    }
} 