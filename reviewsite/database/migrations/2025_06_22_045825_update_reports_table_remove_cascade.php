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
        Schema::table('reports', function (Blueprint $table) {
            // Drop the existing foreign key with cascade delete
            $table->dropForeign(['review_id']);
            
            // Add new foreign key that sets NULL when review is deleted
            $table->foreign('review_id')
                  ->references('id')
                  ->on('reviews')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['review_id']);
            
            // Restore the original cascade delete foreign key
            $table->foreign('review_id')
                  ->references('id')
                  ->on('reviews')
                  ->onDelete('cascade');
        });
    }
};
