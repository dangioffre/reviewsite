<?php

namespace App\Console\Commands;

use App\Jobs\UpdateStreamerProfilesJob;
use Illuminate\Console\Command;

class UpdateStreamerProfilesCommand extends Command
{
    protected $signature = 'streamer:update-profiles';
    protected $description = 'Update streamer profile information from platforms';

    public function handle(): int
    {
        $this->info('Dispatching streamer profile update job...');
        
        UpdateStreamerProfilesJob::dispatch();
        
        $this->info('Profile update job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}