<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class AffiliateLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first game to add affiliate links to
        $game = Product::where('type', 'game')->first();
        
        if ($game) {
            $game->affiliate_links = [
                [
                    'title' => 'Buy on Steam',
                    'url' => 'https://store.steampowered.com/app/123456/',
                    'type' => 'main_game',
                    'price' => '$59.99',
                    'platform' => 'PC',
                    'is_active' => true
                ],
                [
                    'title' => 'Buy on PlayStation Store',
                    'url' => 'https://store.playstation.com/en-us/product/123456',
                    'type' => 'main_game',
                    'price' => '$59.99',
                    'platform' => 'PS5',
                    'is_active' => true
                ],
                [
                    'title' => 'Buy on Xbox Store',
                    'url' => 'https://www.xbox.com/en-us/games/store/123456',
                    'type' => 'main_game',
                    'price' => '$59.99',
                    'platform' => 'Xbox',
                    'is_active' => true
                ],
                [
                    'title' => 'Season Pass',
                    'url' => 'https://store.steampowered.com/app/123457/',
                    'type' => 'season_pass',
                    'price' => '$29.99',
                    'platform' => 'PC',
                    'is_active' => true
                ],
                [
                    'title' => 'Digital Deluxe Edition',
                    'url' => 'https://store.steampowered.com/app/123458/',
                    'type' => 'digital_deluxe',
                    'price' => '$79.99',
                    'platform' => 'PC',
                    'is_active' => true
                ],
                [
                    'title' => 'Collector\'s Edition',
                    'url' => 'https://amazon.com/dp/123456789',
                    'type' => 'collectors_edition',
                    'price' => '$129.99',
                    'platform' => 'Physical',
                    'is_active' => true
                ]
            ];
            
            $game->save();
            
            $this->command->info("Added affiliate links to: {$game->name}");
        } else {
            $this->command->info('No games found to add affiliate links to.');
        }
    }
}
