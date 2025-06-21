<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenrePlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Genres
        $genres = [
            ['name' => 'Action', 'color' => '#E53E3E', 'description' => 'Fast-paced games with combat and adventure'],
            ['name' => 'Adventure', 'color' => '#4CAF50', 'description' => 'Story-driven exploration games'],
            ['name' => 'RPG', 'color' => '#9C27B0', 'description' => 'Role-playing games with character progression'],
            ['name' => 'Strategy', 'color' => '#FF9800', 'description' => 'Games requiring tactical thinking and planning'],
            ['name' => 'Simulation', 'color' => '#2196F3', 'description' => 'Games that simulate real-world activities'],
            ['name' => 'Sports', 'color' => '#00BCD4', 'description' => 'Athletic and competitive sports games'],
            ['name' => 'Racing', 'color' => '#F44336', 'description' => 'High-speed racing and driving games'],
            ['name' => 'Fighting', 'color' => '#795548', 'description' => 'Combat-focused competitive games'],
            ['name' => 'Puzzle', 'color' => '#607D8B', 'description' => 'Mind-bending logic and problem-solving games'],
            ['name' => 'Horror', 'color' => '#424242', 'description' => 'Scary and suspenseful thriller games'],
            ['name' => 'Shooter', 'color' => '#FF5722', 'description' => 'First and third-person shooting games'],
            ['name' => 'Platformer', 'color' => '#8BC34A', 'description' => 'Jump and run platform-based games'],
        ];

        foreach ($genres as $genre) {
            Genre::updateOrCreate(
                ['name' => $genre['name']],
                $genre
            );
        }

        // Create Platforms
        $platforms = [
            ['name' => 'PC', 'icon' => '💻', 'color' => '#2563EB', 'description' => 'Windows, Mac, and Linux computers'],
            ['name' => 'PlayStation', 'icon' => '🎮', 'color' => '#0070F3', 'description' => 'Sony PlayStation consoles'],
            ['name' => 'Xbox', 'icon' => '🎯', 'color' => '#107C10', 'description' => 'Microsoft Xbox consoles'],
            ['name' => 'Nintendo Switch', 'icon' => '🕹️', 'color' => '#E60012', 'description' => 'Nintendo Switch console'],
            ['name' => 'Mobile', 'icon' => '📱', 'color' => '#FF6B6B', 'description' => 'iOS and Android mobile devices'],
            ['name' => 'VR', 'icon' => '🥽', 'color' => '#9C27B0', 'description' => 'Virtual Reality headsets'],
            ['name' => 'Steam Deck', 'icon' => '🚂', 'color' => '#1A9FFF', 'description' => 'Valve Steam Deck handheld'],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(
                ['name' => $platform['name']],
                $platform
            );
        }
    }
}
