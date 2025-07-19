<?php

namespace App\Console\Commands;

use App\Jobs\CheckLiveStatusJob;
use App\Models\StreamerProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAllStreamersLiveStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamers:check-live-status {--platform= : Check only specific platform}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check live status for all approved streamer profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting live status check for all streamers...');

        $query = StreamerProfile::approved()
            ->whereNotNull('oauth_token')
            ->whereNotNull('platform_user_id')
            ->where('platform_user_id', '!=', '');

        // Filter by platform if specified
        if ($platform = $this->option('platform')) {
            $query->platform($platform);
            $this->info("Filtering by platform: {$platform}");
        }

        $streamers = $query->get();
        
        if ($streamers->isEmpty()) {
            $this->warn('No approved streamers found to check.');
            return;
        }

        $this->info("Found {$streamers->count()} streamers to check.");

        $dispatched = 0;
        $skipped = 0;

        foreach ($streamers as $streamer) {
            // Skip if checked very recently (within last 2 minutes) to avoid spam
            if ($streamer->live_status_checked_at && 
                $streamer->live_status_checked_at->diffInMinutes(now()) < 2) {
                $skipped++;
                continue;
            }

            CheckLiveStatusJob::dispatch($streamer);
            $dispatched++;
        }

        $this->info("Dispatched {$dispatched} live status check jobs.");
        
        if ($skipped > 0) {
            $this->info("Skipped {$skipped} streamers (checked recently).");
        }

        Log::info('Live status check command completed', [
            'total_streamers' => $streamers->count(),
            'dispatched_jobs' => $dispatched,
            'skipped' => $skipped,
            'platform_filter' => $this->option('platform')
        ]);
    }
}
