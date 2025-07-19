<?php

namespace App\Console\Commands;

use App\Jobs\CleanupStaleVodsJob;
use Illuminate\Console\Command;

class CleanupStaleVodsCommand extends Command
{
    protected $signature = 'streamer:cleanup-stale-vods';
    protected $description = 'Clean up stale and broken VOD links';

    public function handle(): int
    {
        $this->info('Dispatching stale VOD cleanup job...');
        
        CleanupStaleVodsJob::dispatch();
        
        $this->info('VOD cleanup job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}