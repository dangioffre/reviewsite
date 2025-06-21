<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rename video column to video_url
            $table->renameColumn('video', 'video_url');
            
            // Add missing columns
            $table->date('release_date')->nullable()->after('video_url');
            $table->string('developer')->nullable()->after('release_date');
        });
        
        // Update existing records to have 'game' as default type
        DB::table('products')->whereNull('type')->orWhere('type', '')->update(['type' => 'game']);
        
        // Now we can safely modify the enum
        DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_type_check");
        DB::statement("ALTER TABLE products ALTER COLUMN type TYPE varchar(255)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_type_check CHECK (type IN ('game', 'hardware', 'accessory'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('video_url', 'video');
            $table->dropColumn(['release_date', 'developer']);
        });
        
        // Revert enum
        DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_type_check");
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_type_check CHECK (type IN ('game', 'hardware'))");
    }
};
