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
        // Add is_featured to products table (games)
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('type');
            $table->index('is_featured');
        });

        // Add is_featured to streamer_profiles table
        Schema::table('streamer_profiles', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_approved');
            $table->index('is_featured');
        });

        // Add is_featured to podcasts table
        Schema::table('podcasts', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('approved_by');
            $table->index('is_featured');
        });

        // Add is_featured to lists table
        Schema::table('lists', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_public');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn('is_featured');
        });

        Schema::table('streamer_profiles', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn('is_featured');
        });

        Schema::table('podcasts', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn('is_featured');
        });

        Schema::table('lists', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn('is_featured');
        });
    }
};
