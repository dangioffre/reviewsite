<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some genres and platforms
        $actionGenre = Genre::firstOrCreate(['name' => 'Action', 'slug' => 'action']);
        $rpgGenre = Genre::firstOrCreate(['name' => 'RPG', 'slug' => 'rpg']);
        $adventureGenre = Genre::firstOrCreate(['name' => 'Adventure', 'slug' => 'adventure']);
        $racingGenre = Genre::firstOrCreate(['name' => 'Racing', 'slug' => 'racing']);
        $sportsGenre = Genre::firstOrCreate(['name' => 'Sports', 'slug' => 'sports']);
        $strategyGenre = Genre::firstOrCreate(['name' => 'Strategy', 'slug' => 'strategy']);

        $ps5Platform = Platform::firstOrCreate(['name' => 'PlayStation 5', 'slug' => 'ps5']);
        $xboxPlatform = Platform::firstOrCreate(['name' => 'Xbox Series X/S', 'slug' => 'xbox-series']);
        $pcPlatform = Platform::firstOrCreate(['name' => 'PC', 'slug' => 'pc']);
        $switchPlatform = Platform::firstOrCreate(['name' => 'Nintendo Switch', 'slug' => 'switch']);

        $games = [
            [
                'name' => 'The Legend of Zelda: Tears of the Kingdom',
                'description' => 'An epic adventure game that expands on Breath of the Wild with new mechanics and a captivating story.',
                'genre_id' => $adventureGenre->id,
                'platform_id' => $switchPlatform->id,
                'release_date' => '2023-05-12',
                'developer' => 'Nintendo EPD',
            ],
            [
                'name' => 'Elden Ring',
                'description' => 'A dark fantasy action RPG from FromSoftware and George R.R. Martin, featuring an open world filled with mystery.',
                'genre_id' => $rpgGenre->id,
                'platform_id' => $ps5Platform->id,
                'release_date' => '2022-02-25',
                'developer' => 'FromSoftware',
            ],
            [
                'name' => 'God of War Ragnarök',
                'description' => 'The epic conclusion to the Norse saga, following Kratos and Atreus as they face the end of the world.',
                'genre_id' => $actionGenre->id,
                'platform_id' => $ps5Platform->id,
                'release_date' => '2022-11-09',
                'developer' => 'Santa Monica Studio',
            ],
            [
                'name' => 'Forza Horizon 5',
                'description' => 'An open-world racing game set in a beautiful recreation of Mexico with hundreds of cars to collect.',
                'genre_id' => $racingGenre->id,
                'platform_id' => $xboxPlatform->id,
                'release_date' => '2021-11-09',
                'developer' => 'Playground Games',
            ],
            [
                'name' => 'Cyberpunk 2077',
                'description' => 'A futuristic RPG set in Night City, featuring deep character customization and branching storylines.',
                'genre_id' => $rpgGenre->id,
                'platform_id' => $pcPlatform->id,
                'release_date' => '2020-12-10',
                'developer' => 'CD Projekt RED',
            ],
            [
                'name' => 'FIFA 24',
                'description' => 'The latest installment in the FIFA series with improved gameplay, graphics, and new game modes.',
                'genre_id' => $sportsGenre->id,
                'platform_id' => $ps5Platform->id,
                'release_date' => '2023-09-29',
                'developer' => 'EA Sports',
            ],
            [
                'name' => 'Starfield',
                'description' => 'Bethesda\'s space exploration RPG featuring over 1000 planets to explore in a vast galaxy.',
                'genre_id' => $rpgGenre->id,
                'platform_id' => $xboxPlatform->id,
                'release_date' => '2023-09-06',
                'developer' => 'Bethesda Game Studios',
            ],
            [
                'name' => 'Super Mario Bros. Wonder',
                'description' => 'A new 2D Mario adventure featuring Wonder Flowers that transform levels in unexpected ways.',
                'genre_id' => $adventureGenre->id,
                'platform_id' => $switchPlatform->id,
                'release_date' => '2023-10-20',
                'developer' => 'Nintendo EPD',
            ],
            [
                'name' => 'Age of Empires IV',
                'description' => 'A real-time strategy game that brings the classic Age of Empires gameplay to the modern era.',
                'genre_id' => $strategyGenre->id,
                'platform_id' => $pcPlatform->id,
                'release_date' => '2021-10-28',
                'developer' => 'Relic Entertainment',
            ],
            [
                'name' => 'Spider-Man 2',
                'description' => 'Swing through New York as both Peter Parker and Miles Morales in this superhero action-adventure.',
                'genre_id' => $actionGenre->id,
                'platform_id' => $ps5Platform->id,
                'release_date' => '2023-10-20',
                'developer' => 'Insomniac Games',
            ],
        ];

        foreach ($games as $gameData) {
            Product::create([
                'name' => $gameData['name'],
                'slug' => Str::slug($gameData['name']),
                'description' => $gameData['description'],
                'type' => 'game',
                'genre_id' => $gameData['genre_id'],
                'platform_id' => $gameData['platform_id'],
                'release_date' => $gameData['release_date'],
                'developer' => $gameData['developer'],
            ]);
        }

        $this->command->info('Created 10 game products successfully!');
    }
}
