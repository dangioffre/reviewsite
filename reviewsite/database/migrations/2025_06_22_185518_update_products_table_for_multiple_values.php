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
        Schema::table('products', function (Blueprint $table) {
            // Add new JSON columns for multiple values
            $table->json('genres')->nullable()->after('genre_id');
            $table->json('platforms')->nullable()->after('platform_id');
            $table->json('developers')->nullable()->after('developer');
            $table->json('publishers')->nullable()->after('publisher');
            $table->json('themes')->nullable()->after('theme');
            $table->json('game_modes_list')->nullable()->after('game_modes');
            
            // Keep the old single-value columns for backward compatibility
            // We'll migrate data from these to the new JSON columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'genres',
                'platforms', 
                'developers',
                'publishers',
                'themes',
                'game_modes_list'
            ]);
        });
    }
};
