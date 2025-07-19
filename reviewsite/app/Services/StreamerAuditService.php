<?php

namespace App\Services;

use App\Models\StreamerProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StreamerAuditService
{
    /**
     * Log an administrative action on a streamer profile.
     */
    public static function logAction(string $action, StreamerProfile $profile, array $changes = []): void
    {
        $user = Auth::user();
        
        $logData = [
            'action' => $action,
            'admin_user_id' => $user?->id,
            'admin_user_name' => $user?->name,
            'streamer_profile_id' => $profile->id,
            'channel_name' => $profile->channel_name,
            'platform' => $profile->platform,
            'changes' => $changes,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        Log::channel('single')->info('Streamer Profile Admin Action', $logData);
    }

    /**
     * Log profile approval action.
     */
    public static function logApproval(StreamerProfile $profile): void
    {
        self::logAction('approved', $profile, [
            'is_approved' => ['from' => false, 'to' => true]
        ]);
    }

    /**
     * Log profile rejection action.
     */
    public static function logRejection(StreamerProfile $profile): void
    {
        self::logAction('rejected', $profile, [
            'is_approved' => ['from' => true, 'to' => false]
        ]);
    }

    /**
     * Log profile verification action.
     */
    public static function logVerification(StreamerProfile $profile): void
    {
        self::logAction('verified', $profile, [
            'is_verified' => ['from' => false, 'to' => true]
        ]);
    }

    /**
     * Log profile unverification action.
     */
    public static function logUnverification(StreamerProfile $profile): void
    {
        self::logAction('unverified', $profile, [
            'is_verified' => ['from' => true, 'to' => false]
        ]);
    }

    /**
     * Log profile edit action.
     */
    public static function logEdit(StreamerProfile $profile, array $changes): void
    {
        self::logAction('edited', $profile, $changes);
    }

    /**
     * Log profile deletion action.
     */
    public static function logDeletion(StreamerProfile $profile): void
    {
        self::logAction('deleted', $profile);
    }

    /**
     * Log verification status change.
     */
    public static function logVerificationStatusChange(StreamerProfile $profile, string $newStatus): void
    {
        self::logAction('verification_status_changed', $profile, [
            'verification_status' => ['to' => $newStatus]
        ]);
    }

    /**
     * Log verification rejection.
     */
    public static function logVerificationRejection(StreamerProfile $profile): void
    {
        self::logAction('verification_rejected', $profile, [
            'verification_status' => ['to' => 'rejected'],
            'verification_notes' => $profile->verification_notes
        ]);
    }

    /**
     * Log verification request.
     */
    public static function logVerificationRequest(StreamerProfile $profile): void
    {
        self::logAction('verification_requested', $profile, [
            'verification_status' => ['to' => 'requested']
        ]);
    }
}