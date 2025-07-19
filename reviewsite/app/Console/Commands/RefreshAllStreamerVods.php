<?php

namespace App\Console\Commands;

use App\Jobs\RefreshStreamerVods;
use App\Models\StreamerProfile;
use Illuminate\Console\Command;

class RefreshAllStreamerVods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamer:refresh-vods {--limit=50 : Maximum number of profiles to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh VODs for all approved streamer profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Starting VOD refresh for approved streamer profiles (limit: {$limit})...");

        $profiles = StreamerProfile::approved()
            ->whereNotNull('oauth_token')
            ->limit($limit)
            ->get();

        if ($profiles->isEmpty()) {
            $this->info('No approved streamer profiles with OAuth tokens found.');
            return 0;
        }

        $this->info("Found {$profiles->count()} profiles to process.");

        $bar = $this->output->createProgressBar($profiles->count());
        $bar->start();

        $jobsDispatched = 0;

        foreach ($profiles as $profile) {
            try {
                RefreshStreamerVods::dispatch($profile);
                $jobsDispatched++;
            } catch (\Exception $e) {
                $this->error("Failed to dispatch job for profile {$profile->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully dispatched {$jobsDispatched} VOD refresh jobs.");
        
        return 0;
    }
}
