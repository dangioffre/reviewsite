<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class JobMonitoringService
{
    private const CACHE_PREFIX = 'job_monitoring:';
    private const FAILURE_THRESHOLD = 3;
    private const MONITORING_WINDOW = 3600; // 1 hour

    public function recordJobStart(string $jobClass): void
    {
        $key = $this->getCacheKey($jobClass, 'started');
        Cache::put($key, Carbon::now()->timestamp, self::MONITORING_WINDOW);
        
        Log::info("Job started: {$jobClass}");
    }

    public function recordJobSuccess(string $jobClass): void
    {
        $key = $this->getCacheKey($jobClass, 'success');
        Cache::put($key, Carbon::now()->timestamp, self::MONITORING_WINDOW);
        
        // Reset failure count on success
        $this->resetFailureCount($jobClass);
        
        Log::info("Job completed successfully: {$jobClass}");
    }

    public function recordJobFailure(string $jobClass, string $error): void
    {
        $failureCount = $this->incrementFailureCount($jobClass);
        
        Log::error("Job failed: {$jobClass} - Error: {$error} - Failure count: {$failureCount}");
        
        if ($failureCount >= self::FAILURE_THRESHOLD) {
            $this->sendFailureAlert($jobClass, $failureCount, $error);
        }
    }

    public function getJobStatus(string $jobClass): array
    {
        $startedKey = $this->getCacheKey($jobClass, 'started');
        $successKey = $this->getCacheKey($jobClass, 'success');
        $failureCountKey = $this->getCacheKey($jobClass, 'failures');
        
        return [
            'last_started' => Cache::get($startedKey),
            'last_success' => Cache::get($successKey),
            'failure_count' => Cache::get($failureCountKey, 0),
            'status' => $this->determineJobStatus($jobClass)
        ];
    }

    public function getAllJobStatuses(): array
    {
        $jobClasses = [
            'RefreshOAuthTokensJob',
            'SyncAllStreamerVodsJob',
            'CleanupStaleVodsJob',
            'UpdateStreamerProfilesJob'
        ];
        
        $statuses = [];
        foreach ($jobClasses as $jobClass) {
            $statuses[$jobClass] = $this->getJobStatus($jobClass);
        }
        
        return $statuses;
    }

    public function checkJobHealth(): array
    {
        $statuses = $this->getAllJobStatuses();
        $healthReport = [];
        
        foreach ($statuses as $jobClass => $status) {
            $health = $this->evaluateJobHealth($jobClass, $status);
            $healthReport[$jobClass] = $health;
        }
        
        return $healthReport;
    }

    private function getCacheKey(string $jobClass, string $type): string
    {
        return self::CACHE_PREFIX . $jobClass . ':' . $type;
    }

    private function incrementFailureCount(string $jobClass): int
    {
        $key = $this->getCacheKey($jobClass, 'failures');
        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, self::MONITORING_WINDOW * 24); // Keep failure count longer
        
        return $count;
    }

    private function resetFailureCount(string $jobClass): void
    {
        $key = $this->getCacheKey($jobClass, 'failures');
        Cache::forget($key);
    }

    private function determineJobStatus(string $jobClass): string
    {
        $startedKey = $this->getCacheKey($jobClass, 'started');
        $successKey = $this->getCacheKey($jobClass, 'success');
        $failureCountKey = $this->getCacheKey($jobClass, 'failures');
        
        $lastStarted = Cache::get($startedKey);
        $lastSuccess = Cache::get($successKey);
        $failureCount = Cache::get($failureCountKey, 0);
        
        if ($failureCount >= self::FAILURE_THRESHOLD) {
            return 'critical';
        }
        
        if (!$lastStarted && !$lastSuccess) {
            return 'unknown';
        }
        
        if ($lastStarted && $lastSuccess && $lastSuccess >= $lastStarted) {
            return 'healthy';
        }
        
        if ($lastStarted && (!$lastSuccess || $lastStarted > $lastSuccess)) {
            $timeSinceStart = Carbon::now()->timestamp - $lastStarted;
            if ($timeSinceStart > 3600) { // 1 hour
                return 'stalled';
            }
            return 'running';
        }
        
        return 'unknown';
    }

    private function evaluateJobHealth(string $jobClass, array $status): array
    {
        $health = [
            'status' => $status['status'],
            'issues' => [],
            'recommendations' => []
        ];
        
        if ($status['failure_count'] > 0) {
            $health['issues'][] = "Job has failed {$status['failure_count']} times recently";
        }
        
        if ($status['status'] === 'critical') {
            $health['issues'][] = 'Job has exceeded failure threshold';
            $health['recommendations'][] = 'Check job logs and fix underlying issues';
        }
        
        if ($status['status'] === 'stalled') {
            $health['issues'][] = 'Job appears to be stalled';
            $health['recommendations'][] = 'Check queue workers and job timeout settings';
        }
        
        if (!$status['last_success']) {
            $health['issues'][] = 'No successful runs recorded recently';
            $health['recommendations'][] = 'Verify job is scheduled and running correctly';
        }
        
        return $health;
    }

    private function sendFailureAlert(string $jobClass, int $failureCount, string $error): void
    {
        try {
            // In a real application, you would send this to administrators
            // For now, we'll just log it as a critical alert
            Log::critical("CRITICAL JOB FAILURE ALERT", [
                'job_class' => $jobClass,
                'failure_count' => $failureCount,
                'error' => $error,
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
            // You could also send email notifications here:
            // Mail::to(config('app.admin_email'))->send(new JobFailureAlert($jobClass, $failureCount, $error));
            
        } catch (\Exception $e) {
            Log::error("Failed to send job failure alert: " . $e->getMessage());
        }
    }
}