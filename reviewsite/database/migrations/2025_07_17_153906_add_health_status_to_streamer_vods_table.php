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
        Schema::table('streamer_vods', function (Blueprint $table) {
            $table->enum('health_status', ['healthy', 'unhealthy', 'unchecked'])->default('unchecked')->after('is_manual');
            $table->timestamp('last_health_check_at')->nullable()->after('health_status');
            $table->text('health_check_error')->nullable()->after('last_health_check_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamer_vods', function (Blueprint $table) {
            $table->dropColumn(['health_status', 'last_health_check_at', 'health_check_error']);
        });
    }
};
