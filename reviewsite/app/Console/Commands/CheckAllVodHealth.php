<?php

namespace App\Console\Commands;

use App\Jobs\CheckVodHealth;
use App\Models\StreamerVod;
use Illuminate\Console\Command;

class CheckAllVodHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamer:check-vod-health {--limit=100 : Maximum number of VODs to check} {--days=7 : Only check VODs from the last N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check health of streamer VODs by verifying their URLs are still accessible';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $days = (int) $this->option('days');
        
        $this->info("Starting VOD health check (limit: {$limit}, days: {$days})...");

        $query = StreamerVod::query()
            ->whereHas('streamerProfile', function($q) {
                $q->where('is_approved', true);
            });

        if ($days > 0) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $vods = $query->limit($limit)->get();

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
                CheckVodHealth::dispatch($vod);
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
