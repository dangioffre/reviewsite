<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Fantasy',
                'description' => 'Magical and mythical settings',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Sci-Fi',
                'description' => 'Science fiction and futuristic themes',
                'color' => '#06B6D4',
            ],
            [
                'name' => 'Horror',
                'description' => 'Scary and suspenseful themes',
                'color' => '#EF4444',
            ],
            [
                'name' => 'Adventure',
                'description' => 'Exploration and adventure themes',
                'color' => '#10B981',
            ],
            [
                'name' => 'Mystery',
                'description' => 'Detective and mystery themes',
                'color' => '#6B7280',
            ],
            [
                'name' => 'Historical',
                'description' => 'Based on historical events or periods',
                'color' => '#92400E',
            ],
            [
                'name' => 'Modern',
                'description' => 'Contemporary settings and themes',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Post-Apocalyptic',
                'description' => 'After the end of civilization',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Western',
                'description' => 'Wild West and cowboy themes',
                'color' => '#D97706',
            ],
            [
                'name' => 'Cyberpunk',
                'description' => 'High-tech, low-life futuristic themes',
                'color' => '#EC4899',
            ],
            [
                'name' => 'Steampunk',
                'description' => 'Victorian-era industrial technology',
                'color' => '#A16207',
            ],
            [
                'name' => 'Military',
                'description' => 'War and military combat themes',
                'color' => '#059669',
            ],
            [
                'name' => 'Sports',
                'description' => 'Athletic and sports-related themes',
                'color' => '#DC2626',
            ],
            [
                'name' => 'Racing',
                'description' => 'Vehicle racing and speed themes',
                'color' => '#F97316',
            ],
            [
                'name' => 'Survival',
                'description' => 'Survival and resource management',
                'color' => '#84CC16',
            ],
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
