<?php

namespace App\Services;

use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RssVerificationService
{
    /**
     * Verify if the verification token exists in the RSS feed
     */
    public function verifyToken(Podcast $podcast): bool
    {
        try {
            $rssContent = $this->fetchRssContent($podcast->rss_url);
            
            if (!$rssContent) {
                return false;
            }

            // Check if verification token exists in the RSS content
            $tokenExists = $this->searchForToken($rssContent, $podcast->verification_token);
            
            if ($tokenExists) {
                $podcast->update([
                    'verification_status' => true,
                    'status' => Podcast::STATUS_VERIFIED,
                    'last_rss_check' => now(),
                    'rss_error' => null,
                ]);
                
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('RSS verification failed for podcast: ' . $podcast->name, [
                'podcast_id' => $podcast->id,
                'error' => $e->getMessage(),
            ]);

            $podcast->update([
                'rss_error' => $e->getMessage(),
                'last_rss_check' => now(),
            ]);

            return false;
        }
    }

    /**
     * Import episodes from RSS feed
     */
    public function importEpisodes(Podcast $podcast): int
    {
        try {
            $rssContent = $this->fetchRssContent($podcast->rss_url);
            
            if (!$rssContent) {
                throw new \Exception('Could not fetch RSS content');
            }

            $episodes = $this->parseEpisodes($rssContent);
            $importedCount = 0;

            foreach ($episodes as $episodeData) {
                $episode = Episode::updateOrCreate(
                    [
                        'podcast_id' => $podcast->id,
                        'title' => $episodeData['title'],
                        'published_at' => $episodeData['published_at'],
                    ],
                    $episodeData
                );

                if ($episode->wasRecentlyCreated) {
                    $importedCount++;
                }
            }

            $podcast->update([
                'last_rss_check' => now(),
                'rss_error' => null,
            ]);

            return $importedCount;
        } catch (\Exception $e) {
            Log::error('Episode import failed for podcast: ' . $podcast->name, [
                'podcast_id' => $podcast->id,
                'error' => $e->getMessage(),
            ]);

            $podcast->update([
                'rss_error' => $e->getMessage(),
                'last_rss_check' => now(),
            ]);

            return 0;
        }
    }

    /**
     * Update podcast information from RSS feed
     */
    public function updatePodcastInfo(Podcast $podcast): bool
    {
        try {
            $rssContent = $this->fetchRssContent($podcast->rss_url);
            
            if (!$rssContent) {
                return false;
            }

            $podcastInfo = $this->parsePodcastInfo($rssContent);
            
            // Only update if we have valid data
            if (!empty($podcastInfo)) {
                $podcast->update(array_filter($podcastInfo));
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Podcast info update failed: ' . $podcast->name, [
                'podcast_id' => $podcast->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if RSS feed is accessible and valid
     */
    public function validateRssFeed(string $rssUrl): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(30)->get($rssUrl);
            
            if (!$response->successful()) {
                return [
                    'valid' => false,
                    'error' => 'RSS feed returned HTTP ' . $response->status(),
                ];
            }

            $content = $response->body();
            
            if (empty($content)) {
                return [
                    'valid' => false,
                    'error' => 'RSS feed is empty',
                ];
            }

            // Try to parse as XML
            $xml = simplexml_load_string($content);
            
            if ($xml === false) {
                return [
                    'valid' => false,
                    'error' => 'Invalid XML format',
                ];
            }

            // Check if it's a valid RSS feed
            if (!isset($xml->channel)) {
                return [
                    'valid' => false,
                    'error' => 'Not a valid RSS feed format',
                ];
            }
            
            $channel = $xml->channel;
            $itunes = $channel->children('itunes', true);

            return [
                'valid' => true,
                'title' => (string) $channel->title,
                'description' => (string) $channel->description,
                'website_url' => (string) $channel->link,
                'logo_url' => $this->extractLogoUrl($channel),
                'hosts' => (string) ($itunes->author ?? ''),
                'episode_count' => count($channel->item ?? []),
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch RSS content from URL
     */
    private function fetchRssContent(string $url): ?string
    {
        $response = Http::withoutVerifying()->timeout(30)->get($url);
        
        return $response->successful() ? $response->body() : null;
    }

    /**
     * Search for verification token in RSS content
     */
    private function searchForToken(string $content, string $token): bool
    {
        // Search in the raw content for the token
        return str_contains($content, $token);
    }

    /**
     * Parse episodes from RSS content
     */
    private function parseEpisodes(string $content): array
    {
        $xml = simplexml_load_string($content);
        
        if ($xml === false || !isset($xml->channel->item)) {
            return [];
        }

        $episodes = [];
        
        foreach ($xml->channel->item as $item) {
            $episodes[] = [
                'title' => (string) $item->title,
                'description' => (string) $item->description,
                'published_at' => $this->parseDate((string) $item->pubDate),
                'audio_url' => $this->extractAudioUrl($item),
                'duration' => $this->extractDuration($item),
                'episode_number' => $this->extractEpisodeNumber($item),
            ];
        }

        return $episodes;
    }

    /**
     * Parse podcast information from RSS content
     */
    private function parsePodcastInfo(string $content): array
    {
        $xml = simplexml_load_string($content);
        
        if ($xml === false || !isset($xml->channel)) {
            return [];
        }

        $channel = $xml->channel;
        
        return [
            'description' => (string) $channel->description,
            'logo_url' => $this->extractLogoUrl($channel),
            'website_url' => (string) $channel->link,
        ];
    }

    /**
     * Parse date from RSS format
     */
    private function parseDate(string $dateString): Carbon
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    /**
     * Extract audio URL from RSS item
     */
    private function extractAudioUrl($item): ?string
    {
        // Check enclosure tag
        if (isset($item->enclosure)) {
            foreach ($item->enclosure as $enclosure) {
                $type = (string) $enclosure['type'];
                if (str_starts_with($type, 'audio/')) {
                    return (string) $enclosure['url'];
                }
            }
        }

        return null;
    }

    /**
     * Extract duration from RSS item
     */
    private function extractDuration($item): ?int
    {
        // Check iTunes duration
        $namespaces = $item->getNameSpaces(true);
        
        if (isset($namespaces['itunes'])) {
            $itunes = $item->children($namespaces['itunes']);
            if (isset($itunes->duration)) {
                return $this->parseDuration((string) $itunes->duration);
            }
        }

        return null;
    }

    /**
     * Extract episode number from RSS item
     */
    private function extractEpisodeNumber($item): ?int
    {
        $itunes = $item->children('itunes', true);
        if (isset($itunes->episode)) {
            return (int) $itunes->episode;
        }

        return null;
    }

    private function extractLogoUrl($channel): ?string
    {
        // Check for iTunes image first, as it's often higher quality
        $itunes = $channel->children('itunes', true);
        if (isset($itunes->image) && isset($itunes->image->attributes()->href)) {
            return (string) $itunes->image->attributes()->href;
        }

        // Fallback to the standard RSS image tag
        if (isset($channel->image) && isset($channel->image->url)) {
            return (string) $channel->image->url;
        }

        return null;
    }

    /**
     * Parse duration string to seconds
     */
    private function parseDuration(string $duration): int
    {
        // Check if duration is already in seconds (numeric)
        if (is_numeric($duration)) {
            return (int) $duration;
        }

        // Handle HH:MM:SS, MM:SS, or even SS format
        $parts = explode(':', $duration);
        $seconds = 0;
        $multiplier = 1;

        // Iterate backwards (from seconds to hours)
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $seconds += (int) $parts[$i] * $multiplier;
            $multiplier *= 60;
        }

        return $seconds;
    }
} 