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
            $table->enum('verification_status', ['pending', 'requested', 'in_review', 'verified', 'rejected'])
                  ->default('pending')
                  ->after('is_verified');
            $table->string('verification_token')->nullable()->after('verification_status');
            $table->timestamp('verification_requested_at')->nullable()->after('verification_token');
            $table->timestamp('verification_completed_at')->nullable()->after('verification_requested_at');
            $table->text('verification_notes')->nullable()->after('verification_completed_at');
            $table->foreignId('verified_by')->nullable()->constrained('users')->after('verification_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamer_profiles', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verification_token',
                'verification_requested_at',
                'verification_completed_at',
                'verification_notes',
                'verified_by'
            ]);
        });
    }
};
