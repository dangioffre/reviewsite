<?php

namespace Tests\Unit\Models;

use App\Models\StreamerSocialLink;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerSocialLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_social_link_belongs_to_streamer_profile()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $socialLink = StreamerSocialLink::factory()->create(['streamer_profile_id' => $streamerProfile->id]);

        $this->assertInstanceOf(StreamerProfile::class, $socialLink->streamerProfile);
        $this->assertEquals($streamerProfile->id, $socialLink->streamerProfile->id);
    }

    public function test_platform_scope_filters_by_platform()
    {
        StreamerSocialLink::factory()->create(['platform' => 'twitter']);
        StreamerSocialLink::factory()->create(['platform' => 'instagram']);
        StreamerSocialLink::factory()->create(['platform' => 'twitter']);

        $twitterLinks = StreamerSocialLink::platform('twitter')->get();
        $instagramLinks = StreamerSocialLink::platform('instagram')->get();

        $this->assertCount(2, $twitterLinks);
        $this->assertCount(1, $instagramLinks);
        $this->assertTrue($twitterLinks->every(fn($link) => $link->platform === 'twitter'));
        $this->assertTrue($instagramLinks->every(fn($link) => $link->platform === 'instagram'));
    }

    public function test_get_display_name_or_platform_attribute_returns_display_name_when_available()
    {
        $socialLink = StreamerSocialLink::factory()->create([
            'platform' => 'twitter',
            'display_name' => 'My Twitter'
        ]);

        $this->assertEquals('My Twitter', $socialLink->display_name_or_platform);
    }

    public function test_get_display_name_or_platform_attribute_returns_capitalized_platform_when_no_display_name()
    {
        $socialLink = StreamerSocialLink::factory()->create([
            'platform' => 'twitter',
            'display_name' => null
        ]);

        $this->assertEquals('Twitter', $socialLink->display_name_or_platform);
    }
}