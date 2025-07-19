<?php

namespace Tests\Unit\Models;

use App\Models\StreamerVod;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerVodTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_vod_belongs_to_streamer_profile()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $vod = StreamerVod::factory()->create(['streamer_profile_id' => $streamerProfile->id]);

        $this->assertInstanceOf(StreamerProfile::class, $vod->streamerProfile);
        $this->assertEquals($streamerProfile->id, $vod->streamerProfile->id);
    }

    public function test_manual_scope_filters_manual_vods()
    {
        StreamerVod::factory()->create(['is_manual' => true]);
        StreamerVod::factory()->create(['is_manual' => false]);
        StreamerVod::factory()->create(['is_manual' => true]);

        $manualVods = StreamerVod::manual()->get();

        $this->assertCount(2, $manualVods);
        $this->assertTrue($manualVods->every(fn($vod) => $vod->is_manual));
    }

    public function test_imported_scope_filters_imported_vods()
    {
        StreamerVod::factory()->create(['is_manual' => true]);
        StreamerVod::factory()->create(['is_manual' => false]);
        StreamerVod::factory()->create(['is_manual' => false]);

        $importedVods = StreamerVod::imported()->get();

        $this->assertCount(2, $importedVods);
        $this->assertTrue($importedVods->every(fn($vod) => !$vod->is_manual));
    }

    public function test_get_formatted_duration_attribute_formats_duration_correctly()
    {
        // Test hours, minutes, seconds
        $vod = StreamerVod::factory()->create(['duration_seconds' => 3661]); // 1:01:01
        $this->assertEquals('1:01:01', $vod->formatted_duration);

        // Test minutes and seconds only
        $vod = StreamerVod::factory()->create(['duration_seconds' => 125]); // 2:05
        $this->assertEquals('2:05', $vod->formatted_duration);

        // Test null duration
        $vod = StreamerVod::factory()->create(['duration_seconds' => null]);
        $this->assertNull($vod->formatted_duration);

        // Test zero duration
        $vod = StreamerVod::factory()->create(['duration_seconds' => 0]);
        $this->assertEquals('0:00', $vod->formatted_duration);

        // Test exact hour
        $vod = StreamerVod::factory()->create(['duration_seconds' => 3600]); // 1:00:00
        $this->assertEquals('1:00:00', $vod->formatted_duration);
    }

    public function test_casts_work_correctly()
    {
        $vod = StreamerVod::factory()->create([
            'is_manual' => 1,
            'published_at' => '2024-01-15 14:30:00',
            'last_health_check_at' => '2024-01-15 15:30:00'
        ]);

        $this->assertIsBool($vod->is_manual);
        $this->assertInstanceOf(\Carbon\Carbon::class, $vod->published_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $vod->last_health_check_at);
        $this->assertTrue($vod->is_manual);
    }

    public function test_health_status_scopes_work_correctly()
    {
        StreamerVod::factory()->create(['health_status' => 'healthy']);
        StreamerVod::factory()->create(['health_status' => 'unhealthy']);
        StreamerVod::factory()->create(['health_status' => 'unchecked']);
        StreamerVod::factory()->create(['health_status' => 'healthy']);

        $this->assertCount(2, StreamerVod::healthy()->get());
        $this->assertCount(1, StreamerVod::unhealthy()->get());
        $this->assertCount(1, StreamerVod::unchecked()->get());
    }

    public function test_health_status_helper_methods()
    {
        $healthyVod = StreamerVod::factory()->create(['health_status' => 'healthy']);
        $unhealthyVod = StreamerVod::factory()->create(['health_status' => 'unhealthy']);
        $uncheckedVod = StreamerVod::factory()->create(['health_status' => 'unchecked']);

        $this->assertTrue($healthyVod->isHealthy());
        $this->assertFalse($healthyVod->isUnhealthy());
        $this->assertFalse($healthyVod->isUnchecked());

        $this->assertFalse($unhealthyVod->isHealthy());
        $this->assertTrue($unhealthyVod->isUnhealthy());
        $this->assertFalse($unhealthyVod->isUnchecked());

        $this->assertFalse($uncheckedVod->isHealthy());
        $this->assertFalse($uncheckedVod->isUnhealthy());
        $this->assertTrue($uncheckedVod->isUnchecked());
    }

    public function test_mark_as_healthy_updates_status_and_timestamp()
    {
        $vod = StreamerVod::factory()->create([
            'health_status' => 'unchecked',
            'health_check_error' => 'Previous error'
        ]);

        $vod->markAsHealthy();

        $this->assertEquals('healthy', $vod->fresh()->health_status);
        $this->assertNotNull($vod->fresh()->last_health_check_at);
        $this->assertNull($vod->fresh()->health_check_error);
    }

    public function test_mark_as_unhealthy_updates_status_and_error()
    {
        $vod = StreamerVod::factory()->create(['health_status' => 'healthy']);

        $vod->markAsUnhealthy('Connection timeout');

        $freshVod = $vod->fresh();
        $this->assertEquals('unhealthy', $freshVod->health_status);
        $this->assertNotNull($freshVod->last_health_check_at);
        $this->assertEquals('Connection timeout', $freshVod->health_check_error);
    }
}