<?php

namespace App\Console\Commands;

use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateReviewSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing reviews that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reviews = Review::whereNull('slug')->orWhere('slug', '')->get();
        
        if ($reviews->isEmpty()) {
            $this->info('No reviews need slug generation.');
            return;
        }
        
        $this->info("Found {$reviews->count()} reviews without slugs. Generating...");
        
        $bar = $this->output->createProgressBar($reviews->count());
        $bar->start();
        
        foreach ($reviews as $review) {
            // Generate title if missing
            if (!$review->title) {
                $review->title = 'Review for ' . $review->product->name;
            }
            
            // Generate slug
            $baseSlug = Str::slug($review->title);
            $slug = $baseSlug;
            $counter = 1;
            
            // Ensure uniqueness
            while (Review::where('slug', $slug)->where('id', '!=', $review->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $review->slug = $slug;
            $review->save();
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Slug generation completed!');
    }
}
