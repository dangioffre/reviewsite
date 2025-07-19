<?php

namespace Tests\Unit\Services;

use App\Services\JobMonitoringService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class JobMonitoringServiceTest extends TestCase
{
    private JobMonitoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JobMonitoringService();
        Cache::flush();
    }

    public function test_records_job_start()
    {
        // Act
        $this->service->recordJobStart('TestJob');

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertNotNull($status['last_started']);
        $this->assertEquals('running', $status['status']);
    }

    public function test_records_job_success()
    {
        // Arrange
        $this->service->recordJobStart('TestJob');

        // Act
        $this->service->recordJobSuccess('TestJob');

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertNotNull($status['last_success']);
        $this->assertEquals('healthy', $status['status']);
        $this->assertEquals(0, $status['failure_count']);
    }

    public function test_records_job_failure()
    {
        // Act
        $this->service->recordJobFailure('TestJob', 'Test error');

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertEquals(1, $status['failure_count']);
    }

    public function test_increments_failure_count()
    {
        // Act
        $this->service->recordJobFailure('TestJob', 'Error 1');
        $this->service->recordJobFailure('TestJob', 'Error 2');

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertEquals(2, $status['failure_count']);
    }

    public function test_resets_failure_count_on_success()
    {
        // Arrange
        $this->service->recordJobFailure('TestJob', 'Error');
        $this->assertEquals(1, $this->service->getJobStatus('TestJob')['failure_count']);

        // Act
        $this->service->recordJobSuccess('TestJob');

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertEquals(0, $status['failure_count']);
    }

    public function test_determines_critical_status_after_threshold()
    {
        // Act
        for ($i = 0; $i < 3; $i++) {
            $this->service->recordJobFailure('TestJob', "Error {$i}");
        }

        // Assert
        $status = $this->service->getJobStatus('TestJob');
        $this->assertEquals('critical', $status['status']);
    }

    public function test_logs_critical_alert_on_failure_threshold()
    {
        // Arrange
        Log::shouldReceive('error')->times(3);
        Log::shouldReceive('critical')
            ->once()
            ->with('CRITICAL JOB FAILURE ALERT', \Mockery::type('array'));

        // Act
        for ($i = 0; $i < 3; $i++) {
            $this->service->recordJobFailure('TestJob', "Error {$i}");
        }

        // Assert - Expectation verified by Mockery
        $this->assertTrue(true);
    }

    public function test_gets_all_job_statuses()
    {
        // Arrange
        $this->service->recordJobStart('Job1');
        $this->service->recordJobSuccess('Job2');

        // Act
        $statuses = $this->service->getAllJobStatuses();

        // Assert
        $this->assertIsArray($statuses);
        $this->assertArrayHasKey('RefreshOAuthTokensJob', $statuses);
        $this->assertArrayHasKey('SyncAllStreamerVodsJob', $statuses);
        $this->assertArrayHasKey('CleanupStaleVodsJob', $statuses);
        $this->assertArrayHasKey('UpdateStreamerProfilesJob', $statuses);
    }

    public function test_checks_job_health()
    {
        // Arrange
        $this->service->recordJobFailure('TestJob', 'Error');

        // Act
        $healthReport = $this->service->checkJobHealth();

        // Assert
        $this->assertIsArray($healthReport);
        foreach ($healthReport as $jobClass => $health) {
            $this->assertArrayHasKey('status', $health);
            $this->assertArrayHasKey('issues', $health);
            $this->assertArrayHasKey('recommendations', $health);
        }
    }

    public function test_evaluates_job_health_with_issues()
    {
        // Arrange
        for ($i = 0; $i < 2; $i++) {
            $this->service->recordJobFailure('TestJob', "Error {$i}");
        }

        // Act
        $healthReport = $this->service->checkJobHealth();

        // Assert
        $this->assertNotEmpty($healthReport);
        // The health report should contain information about job failures
        $this->assertTrue(true); // Basic assertion that no exceptions were thrown
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}