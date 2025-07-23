<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgeRating;
use Illuminate\Support\Str;

class AgeRatingSeeder extends Seeder
{
    public function run(): void
    {
        $esrbRatings = [
            'EC' => 'Early Childhood',
            'E' => 'Everyone',
            'E10+' => 'Everyone 10+',
            'T' => 'Teen',
            'M' => 'Mature',
            'AO' => 'Adults Only',
            'RP' => 'Rating Pending',
        ];

        $pegiRatings = [
            '3' => 'PEGI 3',
            '7' => 'PEGI 7',
            '12' => 'PEGI 12',
            '16' => 'PEGI 16',
            '18' => 'PEGI 18',
        ];

        foreach ($esrbRatings as $code => $name) {
            AgeRating::firstOrCreate([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'esrb',
            ]);
        }

        foreach ($pegiRatings as $code => $name) {
            AgeRating::firstOrCreate([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'pegi',
            ]);
        }
    }
} 