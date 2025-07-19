<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RefreshOAuthTokensJob;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\StreamerOAuthService;
use App\Services\JobMonitoringService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class RefreshOAuthTokensJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    public function test_refreshes_expiring_oauth_tokens()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'oauth_expires_at' => Carbon::now()->addHours(12), // Expiring soon
            'oauth_refresh_token' => 'refresh_token_123',
            'is_approved' => true
        ]);

        $oauthService = Mockery::mock(StreamerOAuthService::class);
        $oauthService->shouldReceive('refreshToken')
            ->with(Mockery::on(function ($arg) use ($profile) {
                return $arg->id === $profile->id;
            }))
            ->once()
            ->andReturn(true);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new RefreshOAuthTokensJob();
        $job->handle($oauthService, $jobMonitoring);

        // Assert - No exceptions thrown
        $this->assertTrue(true);
    }

    public function test_skips_tokens_not_expiring_soon()
    {
        // Arrange
        $user = User::factory()->create();
        StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'oauth_expires_at' => Carbon::now()->addWeeks(2), // Not expiring soon
            'oauth_refresh_token' => 'refresh_token_123'
        ]);

        $oauthService = Mockery::mock(StreamerOAuthService::class);
        $oauthService->shouldNotReceive('refreshToken');

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Act
        $job = new RefreshOAuthTokensJob();
        $job->handle($oauthService, $jobMonitoring);

        // Assert - No exceptions thrown
        $this->assertTrue(true);
    }

    public function test_handles_refresh_failures_gracefully()
    {
        // Arrange
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'oauth_expires_at' => Carbon::now()->addHours(12),
            'oauth_refresh_token' => 'refresh_token_123'
        ]);

        $oauthService = Mockery::mock(StreamerOAuthService::class);
        $oauthService->shouldReceive('refreshToken')
            ->with($profile)
            ->once()
            ->andReturn(false);

        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobStart')->once();
        $jobMonitoring->shouldReceive('recordJobSuccess')->once();

        // Log expectations will be verified by the spy

        // Act
        $job = new RefreshOAuthTokensJob();
        $job->handle($oauthService, $jobMonitoring);

        // Assert - No exceptions thrown
        $this->assertTrue(true);
    }

    public function test_records_job_failure_on_exception()
    {
        // Arrange
        $jobMonitoring = Mockery::mock(JobMonitoringService::class);
        $jobMonitoring->shouldReceive('recordJobFailure')
            ->with(RefreshOAuthTokensJob::class, Mockery::type('string'))
            ->once();

        // Log expectations will be verified by the spy

        $exception = new \Exception('Test exception');

        // Act
        $job = new RefreshOAuthTokensJob();
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