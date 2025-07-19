<?php

namespace Tests\Unit\Console;

use App\Console\Commands\CheckVodHealth;
use App\Jobs\CheckVodHealth as CheckVodHealthJob;
use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckVodHealthCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_health_check_jobs_for_recent_vods()
    {
        Queue::fake();

        // Create approved streamer profile with VODs
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
        ]);

        // Create VODs from the last 7 days
        $recentVod1 = StreamerVod::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'created_at' => now()->subDays(3),
        ]);

        $recentVod2 = StreamerVod::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'created_at' => now()->subDays(5),
        ]);

        // Create old VOD (should not be checked)
        $oldVod = StreamerVod::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'created_at' => now()->subDays(10),
        ]);

        $this->artisan('streamer:check-vod-health --days=7 --limit=10')
            ->expectsOutput('Starting VOD health check (limit: 10, days: 7)...')
            ->expectsOutput('Found 2 VODs to check.')
            ->expectsOutput('Successfully dispatched 2 VOD health check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckVodHealthJob::class, 2);
        Queue::assertPushed(CheckVodHealthJob::class, function ($job) use ($recentVod1) {
            return $job->vod->id === $recentVod1->id;
        });
        Queue::assertPushed(CheckVodHealthJob::class, function ($job) use ($recentVod2) {
            return $job->vod->id === $recentVod2->id;
        });
    }

    /** @test */
    public function it_only_checks_vods_from_approved_profiles()
    {
        Queue::fake();

        // Create unapproved streamer profile
        $user = User::factory()->create();
        $unapprovedProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false,
        ]);

        StreamerVod::factory()->create([
            'streamer_profile_id' => $unapprovedProfile->id,
            'created_at' => now()->subDays(1),
        ]);

        $this->artisan('streamer:check-vod-health')
            ->expectsOutput('No VODs found to check.')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    /** @test */
    public function it_respects_limit_parameter()
    {
        Queue::fake();

        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
        ]);

        // Create 5 VODs
        for ($i = 0; $i < 5; $i++) {
            StreamerVod::factory()->create([
                'streamer_profile_id' => $streamerProfile->id,
                'created_at' => now()->subDays($i + 1),
            ]);
        }

        $this->artisan('streamer:check-vod-health --limit=3')
            ->expectsOutput('Found 3 VODs to check.')
            ->expectsOutput('Successfully dispatched 3 VOD health check jobs.')
            ->assertExitCode(0);

        Queue::assertPushed(CheckVodHealthJob::class, 3);
    }

    /** @test */
    public function it_handles_empty_vod_list_gracefully()
    {
        Queue::fake();

        $this->artisan('streamer:check-vod-health')
            ->expectsOutput('No VODs found to check.')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }
}