<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SyncAllStreamerVodsJob;
use App\Jobs\SyncStreamerVodsJob;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use App\Services\JobMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class SyncAllStreamerVodsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_sync_jobs_for_active_profiles()
    {
        // Arrange
        Queue::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $profile1 = StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'is_approved' => true,
            'oauth_token' => 'token_123'
        ]);
        
        $profile2 = StreamerProfile::factory()->create([
            'user_id' => $user2->id,
            'is_approved' => true,
            'oauth_token' => 'token_456'
        ]);

        // Create an inactive profile (should be skipped)
        StreamerProfile::factory()->create([
            'user_id' => User::factory()->create()->id,
            'is_approved' => false,
            'oauth_token' => null
        ]);

        $platformApiService = Mockery::mock(PlatformApiService::class);
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new SyncAllStreamerVodsJob();
        $job->handle($platformApiService, $jobMonitoring);

        // Assert
        Queue::assertPushed(SyncStreamerVodsJob::class, 2);
    }

    public function test_handles_empty_profile_list()
    {
        // Arrange
        Queue::fake();

        $platformApiService = Mockery::mock(PlatformApiService::class);
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new SyncAllStreamerVodsJob();
        $job->handle($platformApiService, $jobMonitoring);

        // Assert
        Queue::assertNothingPushed();
    }

    public function test_records_job_failure_on_exception()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobFailure')
            ->with(SyncAllStreamerVodsJob::class, Mockery::type('string'))
            ->once();

        $exception = new \Exception('Test exception');

        // Act
        $job = new SyncAllStreamerVodsJob();
        $job->failed($exception);

        // Assert - No exceptions thrown
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}