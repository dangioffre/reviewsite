<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateGameImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:update-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update games with placeholder images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $games = Product::where('type', 'game')->whereNull('image')->get();
        
        $this->info("Found {$games->count()} games without images.");
        
        foreach ($games as $game) {
            $game->update([
                'image' => 'https://via.placeholder.com/600x400/27272A/FFFFFF?text=' . urlencode($game->name)
            ]);
            $this->info("Updated image for: {$game->name}");
        }
        
        $this->info("All games updated with placeholder images!");
        return 0;
    }
}
