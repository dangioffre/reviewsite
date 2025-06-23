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
        // Create genres table
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create platforms table
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create hardware table
        Schema::create('hardware', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->string('color')->default('#10B981');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create developers table
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('color')->default('#F59E0B');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create publishers table
        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create themes table
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color')->default('#8B5CF6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create game_modes table
        Schema::create('game_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_modes');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('developers');
        Schema::dropIfExists('hardware');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('genres');
    }
}; 