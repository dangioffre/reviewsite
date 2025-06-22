<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            [
                'name' => 'Nintendo',
                'description' => 'Japanese video game publisher',
                'website' => 'https://www.nintendo.com',
                'country' => 'Japan',
                'color' => '#E60012',
            ],
            [
                'name' => 'Sony Interactive Entertainment',
                'description' => 'PlayStation game publisher',
                'website' => 'https://www.playstation.com',
                'country' => 'Japan',
                'color' => '#003087',
            ],
            [
                'name' => 'Microsoft',
                'description' => 'Xbox game publisher',
                'website' => 'https://www.xbox.com',
                'country' => 'United States',
                'color' => '#00BCF2',
            ],
            [
                'name' => 'Electronic Arts',
                'description' => 'Major game publisher',
                'website' => 'https://www.ea.com',
                'country' => 'United States',
                'color' => '#FF6600',
            ],
            [
                'name' => 'Activision Blizzard',
                'description' => 'Call of Duty and Blizzard games publisher',
                'website' => 'https://www.activisionblizzard.com',
                'country' => 'United States',
                'color' => '#148EFF',
            ],
            [
                'name' => 'Ubisoft',
                'description' => 'French video game publisher',
                'website' => 'https://www.ubisoft.com',
                'country' => 'France',
                'color' => '#0F0F23',
            ],
            [
                'name' => 'Take-Two Interactive',
                'description' => 'Rockstar and 2K Games parent company',
                'website' => 'https://www.take2games.com',
                'country' => 'United States',
                'color' => '#FF6B35',
            ],
            [
                'name' => '2K Games',
                'description' => 'Sports and strategy game publisher',
                'website' => 'https://www.2k.com',
                'country' => 'United States',
                'color' => '#FF6B35',
            ],
            [
                'name' => 'Bandai Namco',
                'description' => 'Japanese entertainment company',
                'website' => 'https://www.bandainamcoent.com',
                'country' => 'Japan',
                'color' => '#FFD700',
            ],
            [
                'name' => 'Square Enix',
                'description' => 'Final Fantasy and Dragon Quest publisher',
                'website' => 'https://www.square-enix.com',
                'country' => 'Japan',
                'color' => '#003DA5',
            ],
            [
                'name' => 'Capcom',
                'description' => 'Street Fighter and Resident Evil publisher',
                'website' => 'https://www.capcom.com',
                'country' => 'Japan',
                'color' => '#0066CC',
            ],
            [
                'name' => 'Sega',
                'description' => 'Sonic and Total War publisher',
                'website' => 'https://www.sega.com',
                'country' => 'Japan',
                'color' => '#1E90FF',
            ],
        ];

        foreach ($publishers as $publisher) {
            Publisher::create($publisher);
        }
    }
}
