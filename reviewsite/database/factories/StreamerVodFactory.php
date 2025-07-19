<?php

namespace Database\Factories;

use App\Models\StreamerVod;
use App\Models\StreamerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StreamerVod>
 */
class StreamerVodFactory extends Factory
{
    protected $model = StreamerVod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'streamer_profile_id' => StreamerProfile::factory(),
            'platform_vod_id' => $this->faker->unique()->numerify('vod_########'),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->optional()->paragraph(),
            'thumbnail_url' => $this->faker->imageUrl(640, 360, 'technics'),
            'vod_url' => $this->faker->url(),
            'duration_seconds' => $this->faker->numberBetween(300, 14400), // 5 minutes to 4 hours
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'is_manual' => false,
            'health_status' => $this->faker->randomElement(['healthy', 'unhealthy', 'unchecked']),
            'last_health_check_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'health_check_error' => null,
        ];
    }

    /**
     * Indicate that the VOD was manually added.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_manual' => true,
        ]);
    }

    /**
     * Indicate that the VOD is healthy.
     */
    public function healthy(): static
    {
        return $this->state(fn (array $attributes) => [
            'health_status' => 'healthy',
            'last_health_check_at' => now(),
            'health_check_error' => null,
        ]);
    }

    /**
     * Indicate that the VOD is unhealthy.
     */
    public function unhealthy(string $error = 'VOD URL is not accessible'): static
    {
        return $this->state(fn (array $attributes) => [
            'health_status' => 'unhealthy',
            'last_health_check_at' => now(),
            'health_check_error' => $error,
        ]);
    }

    /**
     * Indicate that the VOD health is unchecked.
     */
    public function unchecked(): static
    {
        return $this->state(fn (array $attributes) => [
            'health_status' => 'unchecked',
            'last_health_check_at' => null,
            'health_check_error' => null,
        ]);
    }
}