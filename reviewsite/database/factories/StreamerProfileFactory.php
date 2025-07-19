<?php

namespace Database\Factories;

use App\Models\StreamerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StreamerProfile>
 */
class StreamerProfileFactory extends Factory
{
    protected $model = StreamerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platform = $this->faker->randomElement(['twitch', 'youtube', 'kick']);
        $channelName = $this->faker->userName();
        
        return [
            'user_id' => User::factory(),
            'platform' => $platform,
            'platform_user_id' => $this->faker->unique()->numerify('########'),
            'channel_name' => $channelName,
            'channel_url' => $this->getChannelUrl($platform, $channelName),
            'profile_photo_url' => $this->faker->imageUrl(200, 200, 'people'),
            'bio' => $this->faker->paragraph(),
            'is_verified' => false,
            'is_approved' => false,
            'oauth_token' => $this->faker->sha256(),
            'oauth_refresh_token' => $this->faker->sha256(),
            'oauth_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_live' => false,
            'manual_live_override' => null,
            'live_status_checked_at' => null,
        ];
    }

    /**
     * Indicate that the streamer profile is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the streamer profile is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * Indicate that the streamer profile is both verified and approved.
     */
    public function verifiedAndApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'is_approved' => true,
        ]);
    }

    /**
     * Set the platform to Twitch.
     */
    public function twitch(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'twitch',
            'channel_url' => $this->getChannelUrl('twitch', $attributes['channel_name'] ?? $this->faker->userName()),
        ]);
    }

    /**
     * Set the platform to YouTube.
     */
    public function youtube(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'youtube',
            'channel_url' => $this->getChannelUrl('youtube', $attributes['channel_name'] ?? $this->faker->userName()),
        ]);
    }

    /**
     * Set the platform to Kick.
     */
    public function kick(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'kick',
            'channel_url' => $this->getChannelUrl('kick', $attributes['channel_name'] ?? $this->faker->userName()),
        ]);
    }

    /**
     * Get the channel URL based on platform.
     */
    private function getChannelUrl(string $platform, string $channelName): string
    {
        return match ($platform) {
            'twitch' => "https://twitch.tv/{$channelName}",
            'youtube' => "https://youtube.com/@{$channelName}",
            'kick' => "https://kick.com/{$channelName}",
            default => "https://example.com/{$channelName}",
        };
    }
}