<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create player_perspectives table
        Schema::create('player_perspectives', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Create pivot table
        Schema::create('player_perspective_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_perspective_id')->constrained()->onDelete('cascade');
            $table->unique(['product_id', 'player_perspective_id']);
        });

        // Add new fields to products
        Schema::table('products', function (Blueprint $table) {
            $table->string('esrb_rating')->nullable()->after('release_date');
            $table->string('pegi_rating')->nullable()->after('esrb_rating');
            $table->string('official_website')->nullable()->after('pegi_rating');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['esrb_rating', 'pegi_rating', 'official_website']);
        });
        Schema::dropIfExists('player_perspective_product');
        Schema::dropIfExists('player_perspectives');
    }
}; 