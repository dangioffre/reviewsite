<?php

namespace Tests\Unit\Models;

use App\Models\StreamerSchedule;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_schedule_belongs_to_streamer_profile()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $schedule = StreamerSchedule::factory()->create(['streamer_profile_id' => $streamerProfile->id]);

        $this->assertInstanceOf(StreamerProfile::class, $schedule->streamerProfile);
        $this->assertEquals($streamerProfile->id, $schedule->streamerProfile->id);
    }

    public function test_active_scope_filters_active_schedules()
    {
        StreamerSchedule::factory()->create(['is_active' => true]);
        StreamerSchedule::factory()->create(['is_active' => false]);
        StreamerSchedule::factory()->create(['is_active' => true]);

        $activeSchedules = StreamerSchedule::active()->get();

        $this->assertCount(2, $activeSchedules);
        $this->assertTrue($activeSchedules->every(fn($schedule) => $schedule->is_active));
    }

    public function test_get_day_name_attribute_returns_correct_day_names()
    {
        $schedule = StreamerSchedule::factory()->create(['day_of_week' => 0]);
        $this->assertEquals('Sunday', $schedule->day_name);

        $schedule = StreamerSchedule::factory()->create(['day_of_week' => 1]);
        $this->assertEquals('Monday', $schedule->day_name);

        $schedule = StreamerSchedule::factory()->create(['day_of_week' => 6]);
        $this->assertEquals('Saturday', $schedule->day_name);
    }

    public function test_casts_work_correctly()
    {
        $schedule = StreamerSchedule::factory()->create([
            'is_active' => 1,
            'start_time' => '14:30:00',
            'end_time' => '18:45:00'
        ]);

        $this->assertIsBool($schedule->is_active);
        $this->assertTrue($schedule->is_active);
    }
}