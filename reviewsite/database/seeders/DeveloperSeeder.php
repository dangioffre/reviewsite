<?php

namespace Database\Seeders;

use App\Models\Developer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developers = [
            [
                'name' => 'Nintendo',
                'description' => 'Japanese video game company',
                'website' => 'https://www.nintendo.com',
                'country' => 'Japan',
                'color' => '#E60012',
            ],
            [
                'name' => 'Sony Interactive Entertainment',
                'description' => 'PlayStation game developer and publisher',
                'website' => 'https://www.playstation.com',
                'country' => 'Japan',
                'color' => '#003087',
            ],
            [
                'name' => 'Microsoft Game Studios',
                'description' => 'Xbox game developer and publisher',
                'website' => 'https://www.xbox.com',
                'country' => 'United States',
                'color' => '#107C10',
            ],
            [
                'name' => 'Valve Corporation',
                'description' => 'Steam platform and game developer',
                'website' => 'https://www.valvesoftware.com',
                'country' => 'United States',
                'color' => '#1B2838',
            ],
            [
                'name' => 'Epic Games',
                'description' => 'Fortnite and Unreal Engine developer',
                'website' => 'https://www.epicgames.com',
                'country' => 'United States',
                'color' => '#313131',
            ],
            [
                'name' => 'Rockstar Games',
                'description' => 'Grand Theft Auto series developer',
                'website' => 'https://www.rockstargames.com',
                'country' => 'United States',
                'color' => '#FCAF17',
            ],
            [
                'name' => 'CD Projekt RED',
                'description' => 'The Witcher and Cyberpunk developer',
                'website' => 'https://www.cdprojektred.com',
                'country' => 'Poland',
                'color' => '#DC143C',
            ],
            [
                'name' => 'Ubisoft',
                'description' => 'Assassin\'s Creed and Far Cry developer',
                'website' => 'https://www.ubisoft.com',
                'country' => 'France',
                'color' => '#0F0F23',
            ],
            [
                'name' => 'Electronic Arts',
                'description' => 'FIFA and Battlefield publisher',
                'website' => 'https://www.ea.com',
                'country' => 'United States',
                'color' => '#FF6600',
            ],
            [
                'name' => 'Activision Blizzard',
                'description' => 'Call of Duty and World of Warcraft publisher',
                'website' => 'https://www.activisionblizzard.com',
                'country' => 'United States',
                'color' => '#148EFF',
            ],
        ];

        foreach ($developers as $developer) {
            Developer::create($developer);
        }
    }
}
