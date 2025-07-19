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
        Schema::create('streamer_vods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_profile_id')->constrained()->onDelete('cascade');
            $table->string('platform_vod_id');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('vod_url', 500);
            $table->integer('duration_seconds')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->timestamps();
            
            $table->unique(['streamer_profile_id', 'platform_vod_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamer_vods');
    }
};
