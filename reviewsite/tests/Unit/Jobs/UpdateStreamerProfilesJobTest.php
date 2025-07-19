<?php

namespace Tests\Unit\Jobs;

use App\Jobs\UpdateStreamerProfilesJob;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use App\Services\JobMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UpdateStreamerProfilesJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_profile_information_from_platform()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'oauth_token' => 'token_123',
            'profile_photo_url' => 'old_photo.jpg',
            'bio' => 'Old bio',
            'channel_name' => 'OldName'
        ]);

        $platformApiService = Mockery::mock(PlatformApiService::class);
        $platformApiService->shouldReceive('fetchChannelData')
            ->with($profile)
            ->once()
            ->andReturn([
                'profile_photo_url' => 'new_photo.jpg',
                'bio' => 'New bio',
                'channel_name' => 'NewName'
            ]);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new UpdateStreamerProfilesJob();
        $job->handle($platformApiService, $jobMonitoring);

        // Assert
        $profile->refresh();
        $this->assertEquals('new_photo.jpg', $profile->profile_photo_url);
        $this->assertEquals('New bio', $profile->bio);
        $this->assertEquals('NewName', $profile->channel_name);
    }

    public function test_skips_update_when_no_changes()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'oauth_token' => 'token_123',
            'profile_photo_url' => 'same_photo.jpg',
            'bio' => 'Same bio',
            'channel_name' => 'SameName'
        ]);

        $originalUpdatedAt = $profile->updated_at;

        $platformApiService = Mockery::mock(PlatformApiService::class);
        $platformApiService->shouldReceive('fetchChannelData')
            ->with($profile)
            ->once()
            ->andReturn([
                'profile_photo_url' => 'same_photo.jpg',
                'bio' => 'Same bio',
                'channel_name' => 'SameName'
            ]);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new UpdateStreamerProfilesJob();
        $job->handle($platformApiService, $jobMonitoring);

        // Assert
        $profile->refresh();
        $this->assertEquals($originalUpdatedAt, $profile->updated_at);
    }

    public function test_handles_api_failures_gracefully()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'oauth_token' => 'token_123'
        ]);

        $platformApiService = Mockery::mock(PlatformApiService::class);
        $platformApiService->shouldReceive('fetchChannelData')
            ->with($profile)
            ->once()
            ->andThrow(new \Exception('API Error'));

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new UpdateStreamerProfilesJob();
        $job->handle($platformApiService, $jobMonitoring);

        // Assert - No exceptions thrown, job completes
        $this->assertTrue(true);
    }

    public function test_records_job_failure_on_exception()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobFailure')
            ->with(UpdateStreamerProfilesJob::class, Mockery::type('string'))
            ->once();

        $exception = new \Exception('Test exception');

        // Act
        $job = new UpdateStreamerProfilesJob();
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