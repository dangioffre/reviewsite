<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateZeldaPlatform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zelda:update-platform';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Zelda game platform_id to null';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $zelda = Product::where('name', 'like', '%Zelda%')->first();
        
        if ($zelda) {
            $oldPlatform = $zelda->platform ? $zelda->platform->name : 'None';
            $zelda->platform_id = null;
            $zelda->save();
            
            $this->info("Updated Zelda game platform from '{$oldPlatform}' to null");
        } else {
            $this->error('Zelda game not found');
        }
    }
}
