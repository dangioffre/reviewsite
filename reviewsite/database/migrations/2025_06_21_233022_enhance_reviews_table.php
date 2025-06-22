<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Change review column to content and make it longText for markdown
            $table->longText('content')->nullable()->after('review');
            
            // Add new fields
            $table->string('title')->nullable()->after('user_id');
            $table->text('positive_points')->nullable()->after('content');
            $table->text('negative_points')->nullable()->after('positive_points');
            $table->string('platform_played_on')->nullable()->after('negative_points');
            $table->enum('game_status', ['want', 'playing', 'played'])->nullable()->after('platform_played_on');
            $table->boolean('is_published')->default(true)->after('is_staff_review');
            
            // Add slug for individual review pages
            $table->string('slug')->unique()->nullable()->after('title');
        });
        
        // Copy existing review content to content field
        DB::statement('UPDATE reviews SET content = review WHERE content IS NULL');
        
        // Drop the old review column
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Add back the review column
            $table->text('review')->after('user_id');
            
            // Drop new columns
            $table->dropColumn([
                'title',
                'slug',
                'content',
                'positive_points',
                'negative_points',
                'platform_played_on',
                'game_status',
                'is_published'
            ]);
        });
    }
};
