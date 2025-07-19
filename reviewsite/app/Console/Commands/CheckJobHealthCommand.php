<?php

namespace App\Console\Commands;

use App\Services\JobMonitoringService;
use Illuminate\Console\Command;

class CheckJobHealthCommand extends Command
{
    protected $signature = 'streamer:check-job-health';
    protected $description = 'Check the health status of all streamer maintenance jobs';

    public function handle(JobMonitoringService $jobMonitoring): int
    {
        $this->info('Checking job health status...');
        
        $healthReport = $jobMonitoring->checkJobHealth();
        
        $this->table(
            ['Job', 'Status', 'Issues', 'Recommendations'],
            collect($healthReport)->map(function ($health, $jobClass) {
                return [
                    $jobClass,
                    $health['status'],
                    implode('; ', $health['issues'] ?: ['None']),
                    implode('; ', $health['recommendations'] ?: ['None'])
                ];
            })->toArray()
        );
        
        // Check if any jobs are in critical state
        $criticalJobs = collect($healthReport)->filter(function ($health) {
            return $health['status'] === 'critical';
        });
        
        if ($criticalJobs->isNotEmpty()) {
            $this->error('WARNING: Some jobs are in critical state!');
            return Command::FAILURE;
        }
        
        $this->info('All jobs are healthy.');
        return Command::SUCCESS;
    }
}