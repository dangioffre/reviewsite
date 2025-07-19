<?php

namespace App\Console\Commands;

use App\Services\StreamerErrorHandlingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorPlatformHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'streamer:monitor-platform-health 
                           {--platform= : Specific platform to check (twitch, youtube, kick)}
                           {--alert-threshold=20 : Error count threshold for alerts}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor streaming platform API health and send alerts for issues';

    private StreamerErrorHandlingService $errorHandler;

    public function __construct(StreamerErrorHandlingService $errorHandler)
    {
        parent::__construct();
        $this->errorHandler = $errorHandler;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $platforms = $this->option('platform') 
            ? [$this->option('platform')] 
            : ['twitch', 'youtube', 'kick'];

        $alertThreshold = (int) $this->option('alert-threshold');

        $this->info('Monitoring platform health...');

        $healthReport = [];
        $issuesFound = false;

        foreach ($platforms as $platform) {
            $health = $this->checkPlatformHealth($platform, $alertThreshold);
            $healthReport[$platform] = $health;

            if ($health['has_issues']) {
                $issuesFound = true;
                $this->warn("⚠️  {$platform} is experiencing issues:");
                $this->line("   Total errors: {$health['total_errors']}");
                $this->line("   Error breakdown:");
                foreach ($health['error_breakdown'] as $errorType => $count) {
                    if ($count > 0) {
                        $this->line("     - {$errorType}: {$count}");
                    }
                }
                $this->newLine();
            } else {
                $this->info("✅ {$platform} is healthy (Total errors: {$health['total_errors']})");
            }
        }

        // Log health report
        Log::info('Platform health check completed', [
            'health_report' => $healthReport,
            'issues_found' => $issuesFound,
            'alert_threshold' => $alertThreshold
        ]);

        // Send alerts if issues found
        if ($issuesFound) {
            $this->sendHealthAlerts($healthReport);
        }

        return $issuesFound ? 1 : 0;
    }

    /**
     * Check health of a specific platform
     *
     * @param string $platform
     * @param int $alertThreshold
     * @return array
     */
    private function checkPlatformHealth(string $platform, int $alertThreshold): array
    {
        $errorTypes = [
            'api_unavailable',
            'rate_limited', 
            'network_error',
            'authentication_failed',
            'quota_exceeded',
            'api_error'
        ];

        $errorBreakdown = [];
        $totalErrors = 0;

        foreach ($errorTypes as $errorType) {
            $cacheKey = "streamer_error_{$platform}_{$errorType}_count";
            $count = Cache::get($cacheKey, 0);
            $errorBreakdown[$errorType] = $count;
            $totalErrors += $count;
        }

        $hasIssues = $totalErrors >= $alertThreshold;

        // Additional checks
        $additionalInfo = [
            'is_rate_limited' => $this->isPlatformRateLimited($platform),
            'last_successful_call' => $this->getLastSuccessfulCall($platform),
            'error_rate' => $this->calculateErrorRate($platform),
        ];

        return [
            'platform' => $platform,
            'total_errors' => $totalErrors,
            'error_breakdown' => $errorBreakdown,
            'has_issues' => $hasIssues,
            'additional_info' => $additionalInfo,
            'checked_at' => now()->toISOString()
        ];
    }

    /**
     * Check if platform is currently rate limited
     *
     * @param string $platform
     * @return bool
     */
    private function isPlatformRateLimited(string $platform): bool
    {
        $cacheKey = "platform_api_rate_limit_{$platform}";
        return Cache::has($cacheKey);
    }

    /**
     * Get timestamp of last successful API call
     *
     * @param string $platform
     * @return string|null
     */
    private function getLastSuccessfulCall(string $platform): ?string
    {
        $cacheKey = "platform_last_success_{$platform}";
        return Cache::get($cacheKey);
    }

    /**
     * Calculate error rate for platform
     *
     * @param string $platform
     * @return float
     */
    private function calculateErrorRate(string $platform): float
    {
        $totalCalls = Cache::get("platform_total_calls_{$platform}", 0);
        $totalErrors = 0;

        $errorTypes = ['api_unavailable', 'rate_limited', 'network_error', 'authentication_failed', 'quota_exceeded', 'api_error'];
        foreach ($errorTypes as $errorType) {
            $cacheKey = "streamer_error_{$platform}_{$errorType}_count";
            $totalErrors += Cache::get($cacheKey, 0);
        }

        if ($totalCalls === 0) {
            return 0.0;
        }

        return round(($totalErrors / $totalCalls) * 100, 2);
    }

    /**
     * Send health alerts for platforms with issues
     *
     * @param array $healthReport
     */
    private function sendHealthAlerts(array $healthReport): void
    {
        $platformsWithIssues = array_filter($healthReport, fn($health) => $health['has_issues']);

        if (empty($platformsWithIssues)) {
            return;
        }

        $alertMessage = "Platform Health Alert:\n\n";
        
        foreach ($platformsWithIssues as $platform => $health) {
            $alertMessage .= "🚨 {$platform}:\n";
            $alertMessage .= "  - Total errors: {$health['total_errors']}\n";
            $alertMessage .= "  - Error rate: {$health['additional_info']['error_rate']}%\n";
            $alertMessage .= "  - Rate limited: " . ($health['additional_info']['is_rate_limited'] ? 'Yes' : 'No') . "\n";
            
            $topErrors = array_filter($health['error_breakdown'], fn($count) => $count > 0);
            arsort($topErrors);
            $topErrors = array_slice($topErrors, 0, 3, true);
            
            if (!empty($topErrors)) {
                $alertMessage .= "  - Top errors: " . implode(', ', array_map(
                    fn($type, $count) => "{$type} ({$count})",
                    array_keys($topErrors),
                    array_values($topErrors)
                )) . "\n";
            }
            
            $alertMessage .= "\n";
        }

        // Log the alert
        Log::error('Platform health alert triggered', [
            'platforms_with_issues' => array_keys($platformsWithIssues),
            'alert_message' => $alertMessage
        ]);

        // Here you could integrate with notification services like:
        // - Slack webhook
        // - Email alerts
        // - PagerDuty
        // - Discord webhook
        // etc.

        $this->error('Health alerts have been logged. Configure notification integrations as needed.');
    }
}