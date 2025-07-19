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
        Schema::create('streamer_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_profile_id')->constrained()->onDelete('cascade');
            $table->string('platform', 50); // twitter, instagram, discord, etc.
            $table->string('url', 500);
            $table->string('display_name', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamer_social_links');
    }
};
