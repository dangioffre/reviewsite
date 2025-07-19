<?php

namespace App\Console\Commands;

use App\Jobs\CheckVodHealth as CheckVodHealthJob;
use App\Models\StreamerVod;
use Illuminate\Console\Command;

class CheckVodHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamer:check-vod-health {--limit=100 : Maximum number of VODs to check} {--days=7 : Check VODs from the last N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check health of streamer VODs and identify broken links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $days = (int) $this->option('days');
        
        $this->info("Starting VOD health check (limit: {$limit}, days: {$days})...");

        // Get VODs from the last N days, prioritizing recently added ones
        $vods = StreamerVod::whereHas('streamerProfile', function ($query) {
                $query->where('is_approved', true);
            })
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($vods->isEmpty()) {
            $this->info('No VODs found to check.');
            return 0;
        }

        $this->info("Found {$vods->count()} VODs to check.");

        $bar = $this->output->createProgressBar($vods->count());
        $bar->start();

        $jobsDispatched = 0;

        foreach ($vods as $vod) {
            try {
                CheckVodHealthJob::dispatch($vod);
                $jobsDispatched++;
            } catch (\Exception $e) {
                $this->error("Failed to dispatch health check job for VOD {$vod->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully dispatched {$jobsDispatched} VOD health check jobs.");
        
        return 0;
    }
}