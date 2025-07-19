<?php

namespace Tests\Unit;

use App\Jobs\CheckVodHealth;
use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Models\User;
use App\Services\PlatformApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class CheckVodHealthJobTest extends TestCase
{
    use RefreshDatabase;

    private StreamerVod $vod;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->vod = StreamerVod::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'vod_url' => 'https://example.com/test-vod',
        ]);
    }

    /** @test */
    public function it_checks_vod_health_successfully()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('checkVodHealth')
            ->once()
            ->with($this->vod)
            ->andReturn(true);

        Log::shouldReceive('info')
            ->once()
            ->with('VOD health check passed - marked as healthy', [
                'vod_id' => $this->vod->id
            ]);

        $job = new CheckVodHealth($this->vod);
        $job->handle($mockService);

        // Verify VOD was marked as healthy
        $this->vod->refresh();
        $this->assertEquals('healthy', $this->vod->health_status);
        $this->assertNotNull($this->vod->last_health_check_at);
        $this->assertNull($this->vod->health_check_error);
    }

    /** @test */
    public function it_logs_unhealthy_vods()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('checkVodHealth')
            ->once()
            ->with($this->vod)
            ->andReturn(false);

        Log::shouldReceive('warning')
            ->once()
            ->with('VOD health check failed - marked as unhealthy', [
                'vod_id' => $this->vod->id,
                'vod_url' => $this->vod->vod_url,
                'streamer_profile_id' => $this->vod->streamer_profile_id
            ]);

        $job = new CheckVodHealth($this->vod);
        $job->handle($mockService);

        // Verify VOD was marked as unhealthy
        $this->vod->refresh();
        $this->assertEquals('unhealthy', $this->vod->health_status);
        $this->assertNotNull($this->vod->last_health_check_at);
        $this->assertEquals('VOD URL is not accessible', $this->vod->health_check_error);
    }

    /** @test */
    public function it_handles_health_check_errors_and_rethrows_exception()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('checkVodHealth')
            ->once()
            ->andThrow(new \Exception('Network timeout'));

        Log::shouldReceive('error')
            ->once()
            ->with('VOD health check job failed', [
                'vod_id' => $this->vod->id,
                'error' => 'Network timeout'
            ]);

        $job = new CheckVodHealth($this->vod);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network timeout');

        $job->handle($mockService);

        // Verify VOD was marked as unhealthy with error message
        $this->vod->refresh();
        $this->assertEquals('unhealthy', $this->vod->health_status);
        $this->assertNotNull($this->vod->last_health_check_at);
        $this->assertEquals('Health check failed: Network timeout', $this->vod->health_check_error);
    }

    /** @test */
    public function it_logs_permanent_job_failure()
    {
        $exception = new \Exception('Permanent failure');

        Log::shouldReceive('error')
            ->once()
            ->with('VOD health check job failed permanently', [
                'vod_id' => $this->vod->id,
                'error' => 'Permanent failure'
            ]);

        $job = new CheckVodHealth($this->vod);
        $job->failed($exception);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
