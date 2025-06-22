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
            $table->text('story')->nullable()->after('description');
            $table->string('publisher')->nullable()->after('developer');
            $table->string('game_modes')->nullable()->after('publisher');
            $table->string('theme')->nullable()->after('game_modes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['story', 'publisher', 'game_modes', 'theme']);
        });
    }
};
