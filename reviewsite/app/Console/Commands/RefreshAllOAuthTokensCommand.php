<?php

namespace App\Console\Commands;

use App\Jobs\RefreshOAuthTokensJob;
use Illuminate\Console\Command;

class RefreshAllOAuthTokensCommand extends Command
{
    protected $signature = 'streamer:refresh-oauth-tokens';
    protected $description = 'Refresh OAuth tokens for all streamer profiles that are expiring soon';

    public function handle(): int
    {
        $this->info('Dispatching OAuth token refresh job...');
        
        RefreshOAuthTokensJob::dispatch();
        
        $this->info('OAuth token refresh job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}