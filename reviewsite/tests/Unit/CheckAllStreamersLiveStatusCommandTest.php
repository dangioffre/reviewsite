<?php

namespace Tests\Unit;

use App\Console\Commands\CheckAllStreamersLiveStatus;
use App\Jobs\CheckLiveStatusJob;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckAllStreamersLiveStatusCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_command_dispatches_jobs_for_approved_streamers()
    {
        $approvedStreamer = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '12345',
        ]);

        $unapprovedStreamer = StreamerProfile::factory()->create([
            'is_approved' => false,
            'oauth_token' => 'test_token',
            'platform_user_id' => '67890',
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class)
            ->expectsOutput('Starting live status check for all streamers...')
            ->expectsOutput('Found 1 streamers to check.')
            ->expectsOutput('Dispatched 1 live status check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckLiveStatusJob::class, function ($job) use ($approvedStreamer) {
            return $job->streamerProfile->id === $approvedStreamer->id;
        });
    }

    public function test_command_filters_by_platform()
    {
        $twitchStreamer = StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '12345',
        ]);

        $youtubeStreamer = StreamerProfile::factory()->create([
            'platform' => 'youtube',
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '67890',
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class, ['--platform' => 'twitch'])
            ->expectsOutput('Filtering by platform: twitch')
            ->expectsOutput('Found 1 streamers to check.')
            ->expectsOutput('Dispatched 1 live status check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckLiveStatusJob::class, function ($job) use ($twitchStreamer) {
            return $job->streamerProfile->id === $twitchStreamer->id;
        });

        Queue::assertNotPushed(CheckLiveStatusJob::class, function ($job) use ($youtubeStreamer) {
            return $job->streamerProfile->id === $youtubeStreamer->id;
        });
    }

    public function test_command_skips_streamers_without_oauth_token()
    {
        $streamerWithoutToken = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => null,
            'platform_user_id' => '12345',
        ]);

        $streamerWithToken = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '67890',
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class)
            ->expectsOutput('Found 1 streamers to check.')
            ->expectsOutput('Dispatched 1 live status check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckLiveStatusJob::class, function ($job) use ($streamerWithToken) {
            return $job->streamerProfile->id === $streamerWithToken->id;
        });

        Queue::assertNotPushed(CheckLiveStatusJob::class, function ($job) use ($streamerWithoutToken) {
            return $job->streamerProfile->id === $streamerWithoutToken->id;
        });
    }

    public function test_command_skips_streamers_without_platform_user_id()
    {
        // Since platform_user_id is required by database constraints, 
        // we'll test this by creating a streamer with empty string instead of null
        $streamerWithEmptyPlatformId = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '', // Empty string instead of null
        ]);

        $streamerWithPlatformId = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '67890',
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class)
            ->expectsOutput('Found 1 streamers to check.')
            ->expectsOutput('Dispatched 1 live status check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckLiveStatusJob::class, function ($job) use ($streamerWithPlatformId) {
            return $job->streamerProfile->id === $streamerWithPlatformId->id;
        });

        Queue::assertNotPushed(CheckLiveStatusJob::class, function ($job) use ($streamerWithEmptyPlatformId) {
            return $job->streamerProfile->id === $streamerWithEmptyPlatformId->id;
        });
    }

    public function test_command_skips_recently_checked_streamers()
    {
        $recentlyCheckedStreamer = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '12345',
            'live_status_checked_at' => now()->subMinute(), // Checked 1 minute ago
        ]);

        $oldCheckedStreamer = StreamerProfile::factory()->create([
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '67890',
            'live_status_checked_at' => now()->subMinutes(5), // Checked 5 minutes ago
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class)
            ->expectsOutput('Found 2 streamers to check.')
            ->expectsOutput('Dispatched 1 live status check jobs.')
            ->expectsOutput('Skipped 1 streamers (checked recently).')
            ->assertExitCode(0);

        Queue::assertPushed(CheckLiveStatusJob::class, function ($job) use ($oldCheckedStreamer) {
            return $job->streamerProfile->id === $oldCheckedStreamer->id;
        });

        Queue::assertNotPushed(CheckLiveStatusJob::class, function ($job) use ($recentlyCheckedStreamer) {
            return $job->streamerProfile->id === $recentlyCheckedStreamer->id;
        });
    }

    public function test_command_handles_no_streamers_gracefully()
    {
        $this->artisan(CheckAllStreamersLiveStatus::class)
            ->expectsOutput('Starting live status check for all streamers...')
            ->expectsOutput('No approved streamers found to check.')
            ->assertExitCode(0);

        Queue::assertNotPushed(CheckLiveStatusJob::class);
    }

    public function test_command_with_invalid_platform_filter()
    {
        StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'is_approved' => true,
            'oauth_token' => 'test_token',
            'platform_user_id' => '12345',
        ]);

        $this->artisan(CheckAllStreamersLiveStatus::class, ['--platform' => 'invalid'])
            ->expectsOutput('Filtering by platform: invalid')
            ->expectsOutput('No approved streamers found to check.')
            ->assertExitCode(0);

        Queue::assertNotPushed(CheckLiveStatusJob::class);
    }
}