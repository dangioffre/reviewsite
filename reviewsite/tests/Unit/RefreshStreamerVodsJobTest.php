<?php

namespace Tests\Unit;

use App\Jobs\RefreshStreamerVods;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class RefreshStreamerVodsJobTest extends TestCase
{
    use RefreshDatabase;

    private StreamerProfile $streamerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'twitch',
            'is_approved' => true,
            'oauth_token' => 'test_token',
        ]);
    }

    /** @test */
    public function it_refreshes_vods_for_approved_profile_with_token()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->with($this->streamerProfile, 20)
            ->andReturn(5);

        $job = new RefreshStreamerVods($this->streamerProfile);
        $job->handle($mockService);

        // No exceptions should be thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function it_skips_unapproved_profiles()
    {
        $this->streamerProfile->update(['is_approved' => false]);

        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldNotReceive('importVods');

        Log::shouldReceive('info')
            ->once()
            ->with('Skipping VOD refresh for profile', [
                'profile_id' => $this->streamerProfile->id,
                'reason' => 'not_approved_or_no_token'
            ]);

        $job = new RefreshStreamerVods($this->streamerProfile);
        $job->handle($mockService);
    }

    /** @test */
    public function it_skips_profiles_without_oauth_token()
    {
        $this->streamerProfile->update(['oauth_token' => null]);

        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldNotReceive('importVods');

        Log::shouldReceive('info')
            ->once()
            ->with('Skipping VOD refresh for profile', [
                'profile_id' => $this->streamerProfile->id,
                'reason' => 'not_approved_or_no_token'
            ]);

        $job = new RefreshStreamerVods($this->streamerProfile);
        $job->handle($mockService);
    }

    /** @test */
    public function it_logs_successful_vod_refresh()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->with($this->streamerProfile, 20)
            ->andReturn(3);

        Log::shouldReceive('info')
            ->once()
            ->with('VOD refresh completed', [
                'profile_id' => $this->streamerProfile->id,
                'platform' => $this->streamerProfile->platform,
                'imported_count' => 3
            ]);

        $job = new RefreshStreamerVods($this->streamerProfile);
        $job->handle($mockService);
    }

    /** @test */
    public function it_handles_api_errors_and_rethrows_exception()
    {
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->andThrow(new \Exception('API connection failed'));

        Log::shouldReceive('error')
            ->once()
            ->with('VOD refresh failed', [
                'profile_id' => $this->streamerProfile->id,
                'platform' => $this->streamerProfile->platform,
                'error' => 'API connection failed'
            ]);

        $job = new RefreshStreamerVods($this->streamerProfile);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API connection failed');

        $job->handle($mockService);
    }

    /** @test */
    public function it_logs_permanent_job_failure()
    {
        $exception = new \Exception('Permanent failure');

        Log::shouldReceive('error')
            ->once()
            ->with('VOD refresh job failed permanently', [
                'profile_id' => $this->streamerProfile->id,
                'platform' => $this->streamerProfile->platform,
                'error' => 'Permanent failure'
            ]);

        $job = new RefreshStreamerVods($this->streamerProfile);
        $job->failed($exception);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
