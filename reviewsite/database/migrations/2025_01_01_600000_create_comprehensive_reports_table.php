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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who reported
            $table->string('reason'); // Reason for report (inappropriate, spam, etc.)
            $table->text('additional_info')->nullable(); // Additional details from reporter
            $table->string('review_title')->nullable();
            $table->string('review_author_name')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_type')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin notes for resolution
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who resolved
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['review_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}; 