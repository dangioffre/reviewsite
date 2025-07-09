<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create podcasts table
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('rss_url');
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->json('hosts')->nullable(); // Store array of host names
            $table->json('links')->nullable(); // Store social/platform links
            $table->enum('status', ['pending', 'verified', 'approved', 'rejected'])->default('pending');
            $table->string('verification_token')->unique();
            $table->boolean('verification_status')->default(false);
            $table->timestamp('last_rss_check')->nullable();
            $table->text('rss_error')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['owner_id', 'status']);
            $table->index('verification_token');
        });

        // Create podcast team members table
        Schema::create('podcast_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'member'])->default('member');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            // Ensure unique user per podcast
            $table->unique(['podcast_id', 'user_id']);
            $table->index(['podcast_id', 'role']);
        });

        // Create episodes table
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->longText('show_notes')->nullable();
            $table->timestamp('published_at');
            $table->string('audio_url')->nullable();
            $table->string('artwork_url')->nullable();
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->integer('episode_number')->nullable();
            $table->integer('season_number')->nullable();
            $table->string('episode_type')->default('full'); // full, trailer, bonus
            $table->boolean('is_explicit')->default(false);
            $table->json('tags')->nullable();
            $table->timestamps();
            
            // Ensure unique slug per podcast
            $table->unique(['podcast_id', 'slug']);
            $table->index(['podcast_id', 'published_at']);
            $table->index(['published_at', 'episode_number']);
        });

        // Add podcast columns to existing reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('podcast_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('episode_id')->nullable()->constrained()->onDelete('set null');
            
            // Add indexes for performance
            $table->index(['podcast_id', 'created_at']);
            $table->index(['episode_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns from reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['podcast_id']);
            $table->dropForeign(['episode_id']);
            $table->dropColumn(['podcast_id', 'episode_id']);
        });

        // Drop podcast tables in reverse order
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('podcast_team_members');
        Schema::dropIfExists('podcasts');
    }
}; 