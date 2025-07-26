<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GameTipCategory;

class GameTipCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Quick Tips',
                'description' => 'Short, helpful tips for quick gameplay improvements'
            ],
            [
                'name' => 'Unlockables & Secrets',
                'description' => 'Hidden content, unlockable items, and secret discoveries'
            ],
            [
                'name' => 'Exploration Tips',
                'description' => 'Tips for exploring the game world and finding hidden areas'
            ],
            [
                'name' => 'Combat & Boss Strategies',
                'description' => 'Combat techniques and boss fight strategies'
            ],
            [
                'name' => 'Crafting / Systems Help',
                'description' => 'Crafting systems, game mechanics, and system optimization'
            ],
            [
                'name' => 'Challenge Runs / Bonus Tips',
                'description' => 'Advanced strategies for challenge runs and bonus content'
            ],
            [
                'name' => 'Community-Sourced Tips',
                'description' => 'Tips and tricks discovered by the gaming community'
            ]
        ];

        foreach ($categories as $category) {
            GameTipCategory::create($category);
        }
    }
}
