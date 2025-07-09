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
        Schema::create('episode_review_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('attached_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate attachments
            $table->unique(['episode_id', 'review_id']);
            
            // Indexes for performance
            $table->index(['episode_id', 'created_at']);
            $table->index(['review_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_review_attachments');
    }
};
