<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Support\Str;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $podcasts = Podcast::all();

        if ($podcasts->isEmpty()) {
            $this->command->warn('No podcasts found. Please run PodcastSeeder first.');
            return;
        }

        foreach ($podcasts as $podcast) {
            // Skip if this podcast already has episodes (like the admin's podcast)
            if ($podcast->episodes()->count() > 0) {
                continue;
            }

            // Create 3-8 episodes for each podcast
            $episodeCount = rand(3, 8);
            
            for ($i = 1; $i <= $episodeCount; $i++) {
                $title = $faker->sentence(6) . ' - Episode ' . $i;
                $description = $faker->paragraph(3);
                
                Episode::create([
                    'podcast_id' => $podcast->id,
                    'title' => $title,
                    'slug' => 'episode-' . $i,
                    'description' => $description,
                    'show_notes' => $faker->paragraphs(2, true),
                    'published_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'audio_url' => $faker->url,
                    'artwork_url' => $faker->imageUrl(400, 400, 'abstract', true, 'Episode'),
                    'duration' => rand(900, 5400), // 15-90 minutes in seconds
                    'episode_number' => $i,
                    'season_number' => 1,
                    'episode_type' => $faker->randomElement(['full', 'trailer', 'bonus']),
                    'is_explicit' => $faker->boolean(20),
                    'tags' => $faker->words(3),
                ]);
            }
        }

        $this->command->info('Created episodes for ' . $podcasts->count() . ' podcasts!');
    }
} 