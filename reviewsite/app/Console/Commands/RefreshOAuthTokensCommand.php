<?php

namespace App\Console\Commands;

use App\Jobs\RefreshOAuthTokensJob;
use Illuminate\Console\Command;

class RefreshOAuthTokensCommand extends Command
{
    protected $signature = 'streamer:refresh-oauth-tokens';
    protected $description = 'Refresh OAuth tokens for streamer profiles that are expiring soon';

    public function handle(): int
    {
        $this->info('Dispatching OAuth token refresh job...');
        
        RefreshOAuthTokensJob::dispatch();
        
        $this->info('OAuth token refresh job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}