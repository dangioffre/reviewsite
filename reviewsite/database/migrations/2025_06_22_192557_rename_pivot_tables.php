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
        // Rename pivot tables to match Laravel's naming convention (alphabetical order)
        Schema::rename('product_developer', 'developer_product');
        Schema::rename('product_game_mode', 'game_mode_product');
        // product_publisher and product_theme are already correctly named
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the renaming
        Schema::rename('developer_product', 'product_developer');
        Schema::rename('game_mode_product', 'product_game_mode');
    }
};
