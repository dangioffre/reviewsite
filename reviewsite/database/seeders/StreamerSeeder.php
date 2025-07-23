<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StreamerProfile;

class StreamerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create at least 15 streamer profiles with as much info as possible
        StreamerProfile::factory()
            ->count(15)
            ->verifiedAndApproved()
            ->create();

        $this->command->info('Created 10 streamer profiles with max info!');
    }
} 