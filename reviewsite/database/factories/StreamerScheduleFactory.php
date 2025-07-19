<?php

namespace Database\Factories;

use App\Models\StreamerSchedule;
use App\Models\StreamerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StreamerSchedule>
 */
class StreamerScheduleFactory extends Factory
{
    protected $model = StreamerSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(8, 20);
        $endHour = $this->faker->numberBetween($startHour + 1, 23);
        
        return [
            'streamer_profile_id' => StreamerProfile::factory(),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $endHour),
            'timezone' => $this->faker->randomElement(['UTC', 'America/New_York', 'America/Los_Angeles', 'Europe/London']),
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the schedule is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}