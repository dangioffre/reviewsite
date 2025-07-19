<?php

namespace App\Console\Commands;

use App\Jobs\SyncAllStreamerVodsJob;
use Illuminate\Console\Command;

class SyncAllStreamerVodsCommand extends Command
{
    protected $signature = 'streamer:sync-all-vods';
    protected $description = 'Sync VODs for all active streamer profiles';

    public function handle(): int
    {
        $this->info('Dispatching VOD sync job for all active streamer profiles...');
        
        SyncAllStreamerVodsJob::dispatch();
        
        $this->info('VOD sync job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}