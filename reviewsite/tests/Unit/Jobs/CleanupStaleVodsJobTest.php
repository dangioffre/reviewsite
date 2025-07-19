<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CleanupStaleVodsJob;
use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Models\User;
use App\Services\JobMonitoringService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class CleanupStaleVodsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_removes_old_non_manual_vods()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        // Create old VOD (should be removed)
        $oldVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $profile->id,
            'is_manual' => false,
            'published_at' => Carbon::now()->subMonths(7)
        ]);

        // Create recent VOD (should be kept)
        $recentVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $profile->id,
            'is_manual' => false,
            'published_at' => Carbon::now()->subWeeks(2)
        ]);

        // Create old manual VOD (should be kept)
        $manualVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $profile->id,
            'is_manual' => true,
            'published_at' => Carbon::now()->subMonths(8)
        ]);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        Http::fake([
            $recentVod->vod_url => Http::response('', 200)
        ]);

        // Act
        $job = new CleanupStaleVodsJob();
        $job->handle($jobMonitoring);

        // Assert
        $this->assertDatabaseMissing('streamer_vods', ['id' => $oldVod->id]);
        $this->assertDatabaseHas('streamer_vods', ['id' => $recentVod->id]);
        $this->assertDatabaseHas('streamer_vods', ['id' => $manualVod->id]);
    }

    public function test_removes_broken_vod_links()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $brokenVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $profile->id,
            'published_at' => Carbon::now()->subDays(5),
            'vod_url' => 'https://example.com/broken-vod'
        ]);

        $workingVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $profile->id,
            'published_at' => Carbon::now()->subDays(3),
            'vod_url' => 'https://example.com/working-vod'
        ]);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        Http::fake([
            'https://example.com/broken-vod' => Http::response('', 404),
            'https://example.com/working-vod' => Http::response('', 200)
        ]);

        // Act
        $job = new CleanupStaleVodsJob();
        $job->handle($jobMonitoring);

        // Assert
        $this->assertDatabaseMissing('streamer_vods', ['id' => $brokenVod->id]);
        $this->assertDatabaseHas('streamer_vods', ['id' => $workingVod->id]);
    }

    public function test_records_job_failure_on_exception()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobFailure')
            ->with(CleanupStaleVodsJob::class, Mockery::type('string'))
            ->once();

        $exception = new \Exception('Test exception');

        // Act
        $job = new CleanupStaleVodsJob();
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