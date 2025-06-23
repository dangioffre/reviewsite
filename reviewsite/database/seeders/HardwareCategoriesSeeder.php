<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\GameMode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HardwareCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hardware Categories (using Genre model)
        $hardwareCategories = [
            ['name' => 'Controller', 'color' => '#E53E3E', 'description' => 'Gaming controllers and gamepads'],
            ['name' => 'Headset', 'color' => '#9C27B0', 'description' => 'Gaming headsets and audio equipment'],
            ['name' => 'Mouse', 'color' => '#2196F3', 'description' => 'Gaming mice and pointing devices'],
            ['name' => 'Keyboard', 'color' => '#4CAF50', 'description' => 'Gaming keyboards and input devices'],
            ['name' => 'Monitor', 'color' => '#FF9800', 'description' => 'Gaming monitors and displays'],
            ['name' => 'Chair', 'color' => '#795548', 'description' => 'Gaming chairs and seating'],
            ['name' => 'Webcam', 'color' => '#00BCD4', 'description' => 'Webcams and streaming cameras'],
            ['name' => 'Microphone', 'color' => '#F44336', 'description' => 'Microphones and audio recording equipment'],
            ['name' => 'Speaker', 'color' => '#607D8B', 'description' => 'Speakers and audio output devices'],
            ['name' => 'Mousepad', 'color' => '#424242', 'description' => 'Mousepads and desk accessories'],
        ];

        foreach ($hardwareCategories as $category) {
            Genre::updateOrCreate(
                ['name' => $category['name'], 'type' => 'hardware'],
                array_merge($category, ['type' => 'hardware', 'is_active' => true])
            );
        }

        // Hardware Features (using GameMode model)
        $hardwareFeatures = [
            ['name' => 'Wireless', 'color' => '#2196F3', 'description' => 'Wireless connectivity'],
            ['name' => 'RGB Lighting', 'color' => '#9C27B0', 'description' => 'RGB lighting effects'],
            ['name' => 'Mechanical', 'color' => '#4CAF50', 'description' => 'Mechanical switches or components'],
            ['name' => 'Noise Cancelling', 'color' => '#FF9800', 'description' => 'Active noise cancellation'],
            ['name' => 'High DPI', 'color' => '#E53E3E', 'description' => 'High DPI sensor'],
            ['name' => 'Ergonomic', 'color' => '#00BCD4', 'description' => 'Ergonomic design'],
            ['name' => '4K Support', 'color' => '#F44336', 'description' => '4K resolution support'],
            ['name' => 'USB-C', 'color' => '#607D8B', 'description' => 'USB-C connectivity'],
            ['name' => 'Rechargeable', 'color' => '#795548', 'description' => 'Rechargeable battery'],
            ['name' => 'Adjustable', 'color' => '#424242', 'description' => 'Adjustable settings or components'],
            ['name' => 'Hot-Swappable', 'color' => '#8BC34A', 'description' => 'Hot-swappable components'],
            ['name' => 'Low Latency', 'color' => '#FF5722', 'description' => 'Low latency performance'],
        ];

        foreach ($hardwareFeatures as $feature) {
            GameMode::updateOrCreate(
                ['name' => $feature['name'], 'type' => 'hardware'],
                array_merge($feature, ['type' => 'hardware', 'is_active' => true])
            );
        }

        $this->command->info('Hardware categories and features created successfully!');
    }
}
