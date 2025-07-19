<?php

namespace Tests\Unit\Console;

use App\Console\Commands\CheckJobHealthCommand;
use App\Services\JobMonitoringService;
use Illuminate\Console\Command;
use Mockery;
use Tests\TestCase;

class CheckJobHealthCommandTest extends TestCase
{
    public function test_displays_healthy_job_status()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('checkJobHealth')
            ->once()
            ->andReturn([
                'TestJob' => [
                    'status' => 'healthy',
                    'issues' => [],
                    'recommendations' => []
                ]
            ]);

        $this->app->instance(JobMonitoringService::class, $jobMonitoring);

        // Act
        $exitCode = $this->artisan('streamer:check-job-health');

        // Assert
        $exitCode->assertExitCode(Command::SUCCESS);
        $exitCode->expectsOutput('All jobs are healthy.');
    }

    public function test_displays_critical_job_status()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('checkJobHealth')
            ->once()
            ->andReturn([
                'TestJob' => [
                    'status' => 'critical',
                    'issues' => ['Job has failed multiple times'],
                    'recommendations' => ['Check job logs']
                ]
            ]);

        $this->app->instance(JobMonitoringService::class, $jobMonitoring);

        // Act
        $exitCode = $this->artisan('streamer:check-job-health');

        // Assert
        $exitCode->assertExitCode(Command::FAILURE);
        $exitCode->expectsOutput('WARNING: Some jobs are in critical state!');
    }

    public function test_displays_job_health_table()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('checkJobHealth')
            ->once()
            ->andReturn([
                'Job1' => [
                    'status' => 'healthy',
                    'issues' => [],
                    'recommendations' => []
                ],
                'Job2' => [
                    'status' => 'warning',
                    'issues' => ['Some issue'],
                    'recommendations' => ['Some recommendation']
                ]
            ]);

        $this->app->instance(JobMonitoringService::class, $jobMonitoring);

        // Act
        $exitCode = $this->artisan('streamer:check-job-health');

        // Assert
        $exitCode->assertExitCode(Command::SUCCESS);
        // The command should display a table with job information
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}