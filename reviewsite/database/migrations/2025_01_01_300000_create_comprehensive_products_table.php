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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['game', 'hardware', 'tech']);
            $table->text('description')->nullable();
            $table->longText('story')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->date('release_date')->nullable();
            
            // Foreign key relationships
            $table->foreignId('genre_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('platform_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('hardware_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 