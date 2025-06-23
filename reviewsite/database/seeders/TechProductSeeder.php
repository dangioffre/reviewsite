<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Hardware;
use App\Models\Developer;
use Illuminate\Support\Str;

class TechProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create hardware categories
        $controllerHardware = Hardware::firstOrCreate(['name' => 'Controller', 'slug' => 'controller']);
        $headsetHardware = Hardware::firstOrCreate(['name' => 'Headset', 'slug' => 'headset']);
        $mouseHardware = Hardware::firstOrCreate(['name' => 'Gaming Mouse', 'slug' => 'gaming-mouse']);
        $keyboardHardware = Hardware::firstOrCreate(['name' => 'Gaming Keyboard', 'slug' => 'gaming-keyboard']);
        $monitorHardware = Hardware::firstOrCreate(['name' => 'Gaming Monitor', 'slug' => 'gaming-monitor']);
        $chairHardware = Hardware::firstOrCreate(['name' => 'Gaming Chair', 'slug' => 'gaming-chair']);
        $webcamHardware = Hardware::firstOrCreate(['name' => 'Webcam', 'slug' => 'webcam']);
        $microphoneHardware = Hardware::firstOrCreate(['name' => 'Microphone', 'slug' => 'microphone']);

        // Get or create developers for tech products
        $sony = Developer::firstOrCreate([
            'name' => 'Sony',
            'slug' => 'sony'
        ], [
            'description' => 'Japanese multinational conglomerate known for electronics and gaming',
            'country' => 'Japan',
            'color' => '#000000'
        ]);

        $microsoft = Developer::firstOrCreate([
            'name' => 'Microsoft',
            'slug' => 'microsoft'
        ], [
            'description' => 'American technology corporation',
            'country' => 'United States',
            'color' => '#00BCF2'
        ]);

        $steelSeries = Developer::firstOrCreate([
            'name' => 'SteelSeries',
            'slug' => 'steelseries'
        ], [
            'description' => 'Danish gaming peripheral company',
            'country' => 'Denmark',
            'color' => '#FF6000'
        ]);

        $logitech = Developer::firstOrCreate([
            'name' => 'Logitech',
            'slug' => 'logitech'
        ], [
            'description' => 'Swiss-American computer peripherals company',
            'country' => 'Switzerland',
            'color' => '#00B8FC'
        ]);

        $corsair = Developer::firstOrCreate([
            'name' => 'Corsair',
            'slug' => 'corsair'
        ], [
            'description' => 'American computer peripherals and hardware company',
            'country' => 'United States',
            'color' => '#FFFF00'
        ]);

        $asus = Developer::firstOrCreate([
            'name' => 'ASUS',
            'slug' => 'asus'
        ], [
            'description' => 'Taiwanese multinational computer hardware company',
            'country' => 'Taiwan',
            'color' => '#000080'
        ]);

        $secretlab = Developer::firstOrCreate([
            'name' => 'Secretlab',
            'slug' => 'secretlab'
        ], [
            'description' => 'Singaporean gaming chair company',
            'country' => 'Singapore',
            'color' => '#FF0000'
        ]);

        $elgato = Developer::firstOrCreate([
            'name' => 'Elgato',
            'slug' => 'elgato'
        ], [
            'description' => 'German technology company specializing in content creation tools',
            'country' => 'Germany',
            'color' => '#101014'
        ]);

        $blueMicrophones = Developer::firstOrCreate([
            'name' => 'Blue Microphones',
            'slug' => 'blue-microphones'
        ], [
            'description' => 'American microphone company',
            'country' => 'United States',
            'color' => '#0066FF'
        ]);

        $razer = Developer::firstOrCreate([
            'name' => 'Razer',
            'slug' => 'razer'
        ], [
            'description' => 'Singaporean-American gaming hardware company',
            'country' => 'Singapore',
            'color' => '#00FF00'
        ]);

        $techProducts = [
            [
                'name' => 'PlayStation 5 DualSense Controller',
                'description' => 'Revolutionary wireless controller with haptic feedback, adaptive triggers, and built-in microphone for an immersive gaming experience.',
                'hardware_id' => $controllerHardware->id,
                'release_date' => '2020-11-12',
                'developer' => $sony,
            ],
            [
                'name' => 'Xbox Wireless Controller',
                'description' => 'Ergonomic wireless controller with textured grip, hybrid D-pad, and seamless compatibility across Xbox and PC.',
                'hardware_id' => $controllerHardware->id,
                'release_date' => '2020-11-10',
                'developer' => $microsoft,
            ],
            [
                'name' => 'SteelSeries Arctis 7P Wireless Headset',
                'description' => 'Premium wireless gaming headset with lossless 2.4GHz connection, ClearCast microphone, and 24-hour battery life.',
                'hardware_id' => $headsetHardware->id,
                'release_date' => '2020-09-15',
                'developer' => $steelSeries,
            ],
            [
                'name' => 'Logitech G Pro X Superlight',
                'description' => 'Ultra-lightweight wireless gaming mouse with HERO 25K sensor, weighing less than 63 grams for competitive gaming.',
                'hardware_id' => $mouseHardware->id,
                'release_date' => '2020-12-03',
                'developer' => $logitech,
            ],
            [
                'name' => 'Corsair K70 RGB MK.2 Mechanical Keyboard',
                'description' => 'Premium mechanical gaming keyboard with Cherry MX switches, per-key RGB lighting, and aircraft-grade aluminum frame.',
                'hardware_id' => $keyboardHardware->id,
                'release_date' => '2018-03-15',
                'developer' => $corsair,
            ],
            [
                'name' => 'ASUS ROG Swift PG279QM 27" Gaming Monitor',
                'description' => '27-inch 1440p IPS gaming monitor with 240Hz refresh rate, G-SYNC compatibility, and HDR400 support.',
                'hardware_id' => $monitorHardware->id,
                'release_date' => '2021-06-01',
                'developer' => $asus,
            ],
            [
                'name' => 'Secretlab TITAN Evo 2022 Gaming Chair',
                'description' => 'Ergonomic gaming chair with cold-cure foam, 4-way lumbar support, and premium NEO Hybrid Leatherette upholstery.',
                'hardware_id' => $chairHardware->id,
                'release_date' => '2022-01-15',
                'developer' => $secretlab,
            ],
            [
                'name' => 'Elgato Facecam Pro 4K Webcam',
                'description' => 'Professional 4K60 webcam with Sony sensor, advanced light correction, and premium glass lens for content creation.',
                'hardware_id' => $webcamHardware->id,
                'release_date' => '2023-05-10',
                'developer' => $elgato,
            ],
            [
                'name' => 'Blue Yeti X Professional USB Microphone',
                'description' => 'Professional condenser microphone with four-capsule array, real-time LED meter, and smart knob for precise control.',
                'hardware_id' => $microphoneHardware->id,
                'release_date' => '2019-08-20',
                'developer' => $blueMicrophones,
            ],
            [
                'name' => 'Razer DeathAdder V3 Pro Wireless Mouse',
                'description' => 'Flagship wireless gaming mouse with Focus Pro 30K sensor, 90-hour battery life, and HyperSpeed wireless technology.',
                'hardware_id' => $mouseHardware->id,
                'release_date' => '2022-09-15',
                'developer' => $razer,
            ],
        ];

        foreach ($techProducts as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'type' => 'hardware',
                'hardware_id' => $productData['hardware_id'],
                'release_date' => $productData['release_date'],
            ]);

            // Attach the developer relationship
            $product->developers()->attach($productData['developer']->id);
        }

        $this->command->info('Created 10 tech products with developer relationships successfully!');
    }
}
