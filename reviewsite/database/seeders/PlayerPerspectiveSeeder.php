<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerPerspective;
use Illuminate\Support\Str;

class PlayerPerspectiveSeeder extends Seeder
{
    public function run(): void
    {
        $perspectives = [
            'Auditory',
            'Bird view / Isometric',
            'First person',
            'Side view',
            'Text',
            'Third person',
            'Virtual Reality',
        ];

        foreach ($perspectives as $name) {
            PlayerPerspective::firstOrCreate([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
} 