<?php

namespace Database\Factories;

use App\Models\StreamerSocialLink;
use App\Models\StreamerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StreamerSocialLink>
 */
class StreamerSocialLinkFactory extends Factory
{
    protected $model = StreamerSocialLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platform = $this->faker->randomElement(['twitter', 'instagram', 'discord', 'tiktok', 'facebook']);
        
        return [
            'streamer_profile_id' => StreamerProfile::factory(),
            'platform' => $platform,
            'url' => $this->getSocialUrl($platform),
            'display_name' => $this->faker->optional()->userName(),
        ];
    }

    /**
     * Get a social media URL based on platform.
     */
    private function getSocialUrl(string $platform): string
    {
        $username = $this->faker->userName();
        
        return match ($platform) {
            'twitter' => "https://twitter.com/{$username}",
            'instagram' => "https://instagram.com/{$username}",
            'discord' => "https://discord.gg/{$this->faker->lexify('???????')}",
            'tiktok' => "https://tiktok.com/@{$username}",
            'facebook' => "https://facebook.com/{$username}",
            default => "https://example.com/{$username}",
        };
    }
}