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
        Schema::create('streamer_showcased_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_user_status_id')->constrained()->onDelete('cascade');
            $table->integer('display_order')->default(0); // Order for displaying games
            $table->text('showcase_note')->nullable(); // Optional note about why this game is showcased
            $table->timestamps();
            
            // Ensure a streamer can't showcase the same game twice
            $table->unique(['streamer_profile_id', 'game_user_status_id']);
            
            // Index for ordering
            $table->index(['streamer_profile_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamer_showcased_games');
    }
};
