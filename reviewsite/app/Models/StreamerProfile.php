<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StreamerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'platform_user_id',
        'channel_name',
        'channel_url',
        'profile_photo_url',
        'bio',
        'is_verified',
        'is_approved',
        'oauth_token',
        'oauth_refresh_token',
        'oauth_expires_at',
        'is_live',
        'live_status_checked_at',
        'manual_live_override',
        'verification_status',
        'verification_token',
        'verification_requested_at',
        'verification_completed_at',
        'verification_notes',
        'verified_by',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_live' => 'boolean',
        'manual_live_override' => 'boolean',
        'oauth_expires_at' => 'datetime',
        'live_status_checked_at' => 'datetime',
        'verification_requested_at' => 'datetime',
        'verification_completed_at' => 'datetime',
    ];

    protected $hidden = [
        'oauth_token',
        'oauth_refresh_token',
    ];

    /**
     * Get the user that owns the streamer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedules for the streamer profile.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(StreamerSchedule::class);
    }

    /**
     * Get the VODs for the streamer profile.
     */
    public function vods(): HasMany
    {
        return $this->hasMany(StreamerVod::class);
    }

    /**
     * Get the social links for the streamer profile.
     */
    public function socialLinks(): HasMany
    {
        return $this->hasMany(StreamerSocialLink::class);
    }

    /**
     * Get the followers for the streamer profile.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'streamer_followers')
                    ->withPivot('notification_preferences')
                    ->withTimestamps();
    }

    /**
     * Get the reviews posted by this streamer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the showcased games for this streamer profile.
     */
    public function showcasedGames(): HasMany
    {
        return $this->hasMany(StreamerShowcasedGame::class)->ordered();
    }

    /**
     * Get the user who verified this profile.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include approved profiles.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include verified profiles.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to filter by platform.
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Check if the streamer is currently live.
     */
    public function isLive(): bool
    {
        // Manual override takes precedence
        if ($this->manual_live_override !== null) {
            return $this->manual_live_override;
        }
        
        return $this->is_live ?? false;
    }

    /**
     * Scope a query to only include live streamers.
     */
    public function scopeLive($query)
    {
        return $query->where(function ($q) {
            $q->where('manual_live_override', true)
              ->orWhere(function ($subQ) {
                  $subQ->whereNull('manual_live_override')
                       ->where('is_live', true);
              });
        });
    }

    /**
     * Update live status from platform API.
     */
    public function updateLiveStatus(bool $isLive): void
    {
        $this->update([
            'is_live' => $isLive,
            'live_status_checked_at' => now(),
        ]);
    }

    /**
     * Set manual live status override.
     */
    public function setManualLiveOverride(?bool $isLive): void
    {
        $this->update([
            'manual_live_override' => $isLive,
        ]);
    }

    /**
     * Get the display name for the streamer.
     */
    public function getDisplayName(): string
    {
        return $this->channel_name;
    }

    /**
     * Check if the streamer can post reviews.
     */
    public function canPostReviews(): bool
    {
        return $this->is_approved; // Only need approval, verification removed
    }

    /**
     * Refresh platform data.
     */
    public function refreshPlatformData(): void
    {
        // This will be implemented in later tasks with platform API integration
    }

    /**
     * Check if profile is verified (always true now since OAuth = verified).
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }
}