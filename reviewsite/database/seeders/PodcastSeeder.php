<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Support\Str;

class PodcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $users = User::inRandomOrder()->take(10)->get();
        if ($users->count() < 10) {
            $users = User::factory()->count(10)->create();
        }

        foreach (range(1, 10) as $i) {
            $name = $faker->catchPhrase . ' Podcast';
            $slug = Podcast::generateUniqueSlug($name);
            $hosts = [$faker->name, $faker->name];
            $links = [
                'website' => $faker->url,
                'twitter' => 'https://twitter.com/' . $faker->userName,
                'youtube' => 'https://youtube.com/@' . $faker->userName,
            ];
            $owner = $users[$i - 1];
            Podcast::create([
                'owner_id' => $owner->id,
                'name' => $name,
                'slug' => $slug,
                'description' => $faker->paragraph(3),
                'rss_url' => $faker->url,
                'logo_url' => $faker->imageUrl(300, 300, 'cats', true, 'Podcast'),
                'website_url' => $faker->url,
                'hosts' => $hosts,
                'links' => $links,
                'status' => $faker->randomElement(['pending', 'verified', 'approved', 'rejected']),
                'verification_token' => Podcast::generateUniqueToken(),
                'verification_status' => $faker->boolean(80),
                'last_rss_check' => $faker->dateTimeBetween('-1 month', 'now'),
                'rss_error' => $faker->boolean(10) ? $faker->sentence : null,
                'approved_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-1 month', 'now') : null,
                'approved_by' => $faker->boolean(70) ? $users->random()->id : null,
                'is_featured' => $faker->boolean(30),
                'admin_notes' => $faker->boolean(20) ? $faker->sentence : null,
            ]);
        }

        $gamingPodcastNames = [
            'Game Masters Unlocked',
            'Pixel Pushers Podcast',
            'The Save Point',
            'Boss Fight Banter',
            'Level Up Lounge',
            'Retro Respawn',
            'Indie Game Insider',
            'Next Gen Now',
            'Speedrun Sessions',
            'The Co-Op Couch',
        ];
        foreach ($gamingPodcastNames as $name) {
            $slug = Podcast::generateUniqueSlug($name);
            $hosts = [$faker->userName, $faker->userName];
            $links = [
                'website' => $faker->url,
                'twitter' => 'https://twitter.com/' . $faker->userName,
                'twitch' => 'https://twitch.tv/' . $faker->userName,
                'youtube' => 'https://youtube.com/@' . $faker->userName,
                'discord' => 'https://discord.gg/' . Str::random(8),
            ];
            $owner = $users->random();
            Podcast::create([
                'owner_id' => $owner->id,
                'name' => $name,
                'slug' => $slug,
                'description' => $faker->sentence . ' A podcast all about ' . $faker->randomElement(['gaming news', 'retro games', 'speedrunning', 'indie games', 'game development', 'multiplayer madness', 'eSports', 'console wars', 'PC gaming', 'game reviews']) . '.',
                'rss_url' => $faker->url,
                'logo_url' => $faker->imageUrl(300, 300, 'technics', true, 'Gaming Podcast'),
                'website_url' => $faker->url,
                'hosts' => $hosts,
                'links' => $links,
                'status' => $faker->randomElement(['pending', 'verified', 'approved', 'rejected']),
                'verification_token' => Podcast::generateUniqueToken(),
                'verification_status' => $faker->boolean(80),
                'last_rss_check' => $faker->dateTimeBetween('-1 month', 'now'),
                'rss_error' => $faker->boolean(10) ? $faker->sentence : null,
                'approved_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-1 month', 'now') : null,
                'approved_by' => $faker->boolean(70) ? $users->random()->id : null,
                'is_featured' => $faker->boolean(50),
                'admin_notes' => $faker->boolean(20) ? $faker->sentence : null,
            ]);
        }

        // Ensure at least 15 podcasts exist
        $currentCount = Podcast::count();
        $toAdd = 15 - $currentCount;
        if ($toAdd > 0) {
            foreach (range(1, $toAdd) as $i) {
                $name = $faker->catchPhrase . ' Podcast';
                $slug = Podcast::generateUniqueSlug($name);
                $hosts = [$faker->name, $faker->name];
                $links = [
                    'website' => $faker->url,
                    'twitter' => 'https://twitter.com/' . $faker->userName,
                    'youtube' => 'https://youtube.com/@' . $faker->userName,
                ];
                $owner = $users->random();
                Podcast::create([
                    'owner_id' => $owner->id,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $faker->paragraph(3),
                    'rss_url' => $faker->url,
                    'logo_url' => $faker->imageUrl(300, 300, 'cats', true, 'Podcast'),
                    'website_url' => $faker->url,
                    'hosts' => $hosts,
                    'links' => $links,
                    'status' => $faker->randomElement(['pending', 'verified', 'approved', 'rejected']),
                    'verification_token' => Podcast::generateUniqueToken(),
                    'verification_status' => $faker->boolean(80),
                    'last_rss_check' => $faker->dateTimeBetween('-1 month', 'now'),
                    'rss_error' => $faker->boolean(10) ? $faker->sentence : null,
                    'approved_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-1 month', 'now') : null,
                    'approved_by' => $faker->boolean(70) ? $users->random()->id : null,
                    'is_featured' => $faker->boolean(30),
                    'admin_notes' => $faker->boolean(20) ? $faker->sentence : null,
                ]);
            }
        }

        $this->command->info('Created 10 podcasts with max info!');
    }
} 