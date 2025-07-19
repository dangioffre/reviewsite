<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamerSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_profile_id',
        'platform',
        'url',
        'display_name',
    ];

    /**
     * Get the streamer profile that owns the social link.
     */
    public function streamerProfile(): BelongsTo
    {
        return $this->belongsTo(StreamerProfile::class);
    }

    /**
     * Scope a query to filter by platform.
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Get the display name or fallback to platform name.
     */
    public function getDisplayNameOrPlatformAttribute(): string
    {
        return $this->display_name ?: ucfirst($this->platform);
    }
}