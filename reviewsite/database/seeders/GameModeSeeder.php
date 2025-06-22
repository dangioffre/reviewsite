<?php

namespace Database\Seeders;

use App\Models\GameMode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gameModes = [
            [
                'name' => 'Single-player',
                'description' => 'Games designed for one player',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Multiplayer',
                'description' => 'Games that support multiple players',
                'color' => '#10B981',
            ],
            [
                'name' => 'Co-op',
                'description' => 'Cooperative gameplay with other players',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Competitive',
                'description' => 'Player vs Player competitive gameplay',
                'color' => '#EF4444',
            ],
            [
                'name' => 'Online',
                'description' => 'Online multiplayer functionality',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Local Multiplayer',
                'description' => 'Local multiplayer on same device',
                'color' => '#06B6D4',
            ],
            [
                'name' => 'Split-screen',
                'description' => 'Split-screen local multiplayer',
                'color' => '#84CC16',
            ],
            [
                'name' => 'Battle Royale',
                'description' => 'Large-scale competitive survival',
                'color' => '#F97316',
            ],
            [
                'name' => 'MMO',
                'description' => 'Massively Multiplayer Online',
                'color' => '#EC4899',
            ],
            [
                'name' => 'Turn-based',
                'description' => 'Turn-based gameplay mechanics',
                'color' => '#6B7280',
            ],
        ];

        foreach ($gameModes as $gameMode) {
            GameMode::create($gameMode);
        }
    }
}
