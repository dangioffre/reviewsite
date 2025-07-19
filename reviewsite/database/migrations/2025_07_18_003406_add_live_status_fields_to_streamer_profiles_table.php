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
        Schema::table('streamer_profiles', function (Blueprint $table) {
            $table->boolean('is_live')->default(false)->after('is_approved');
            $table->timestamp('live_status_checked_at')->nullable()->after('is_live');
            $table->boolean('manual_live_override')->nullable()->after('live_status_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamer_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_live', 'live_status_checked_at', 'manual_live_override']);
        });
    }
};
