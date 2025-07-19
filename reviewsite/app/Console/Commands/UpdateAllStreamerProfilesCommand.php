<?php

namespace App\Console\Commands;

use App\Jobs\UpdateStreamerProfileDataJob;
use App\Models\StreamerProfile;
use Illuminate\Console\Command;

class UpdateAllStreamerProfilesCommand extends Command
{
    protected $signature = 'streamer:update-all-profiles';
    protected $description = 'Update profile information for all active streamer profiles from their platforms';

    public function handle(): int
    {
        $this->info('Starting profile data update for all active streamer profiles...');
        
        $activeProfiles = StreamerProfile::where('is_approved', true)
            ->whereNotNull('oauth_token')
            ->get();

        $this->info("Found {$activeProfiles->count()} active streamer profiles");

        foreach ($activeProfiles as $profile) {
            UpdateStreamerProfileDataJob::dispatch($profile);
            $this->line("Dispatched profile update job for profile {$profile->id} ({$profile->channel_name})");
        }
        
        $this->info('All profile update jobs dispatched successfully.');
        
        return Command::SUCCESS;
    }
}