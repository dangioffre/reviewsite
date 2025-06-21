<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hardware;
use Illuminate\Support\Str;

class HardwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hardware = [
            [
                'name' => 'PlayStation 5',
                'slug' => 'playstation-5',
                'description' => 'Sony\'s flagship gaming console with ultra-high speed SSD, haptic feedback, and 3D audio.',
                'color' => '#0070F3',
                'is_active' => true,
            ],
            [
                'name' => 'Xbox Series X',
                'slug' => 'xbox-series-x',
                'description' => 'Microsoft\'s most powerful console with 4K gaming, ray tracing, and Quick Resume.',
                'color' => '#107C10',
                'is_active' => true,
            ],
            [
                'name' => 'Xbox Series S',
                'slug' => 'xbox-series-s',
                'description' => 'Compact next-gen console with digital-only gaming and 1440p performance.',
                'color' => '#00BCF2',
                'is_active' => true,
            ],
            [
                'name' => 'Nintendo Switch',
                'slug' => 'nintendo-switch',
                'description' => 'Hybrid gaming console that transforms from home console to portable device.',
                'color' => '#E60012',
                'is_active' => true,
            ],
            [
                'name' => 'Steam Deck',
                'slug' => 'steam-deck',
                'description' => 'Valve\'s handheld gaming PC with full Steam library compatibility.',
                'color' => '#1B2838',
                'is_active' => true,
            ],
            [
                'name' => 'Gaming PC',
                'slug' => 'gaming-pc',
                'description' => 'High-performance desktop computers optimized for gaming.',
                'color' => '#FF6B35',
                'is_active' => true,
            ],
            [
                'name' => 'PlayStation 4',
                'slug' => 'playstation-4',
                'description' => 'Sony\'s previous generation console with extensive game library.',
                'color' => '#003087',
                'is_active' => true,
            ],
            [
                'name' => 'Xbox One',
                'slug' => 'xbox-one',
                'description' => 'Microsoft\'s previous generation console with multimedia capabilities.',
                'color' => '#5BB85B',
                'is_active' => true,
            ],
        ];

        foreach ($hardware as $item) {
            Hardware::create($item);
        }
    }
}
