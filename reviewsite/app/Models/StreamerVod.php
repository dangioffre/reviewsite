<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamerVod extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_profile_id',
        'platform_vod_id',
        'title',
        'description',
        'thumbnail_url',
        'vod_url',
        'duration_seconds',
        'published_at',
        'is_manual',
        'health_status',
        'last_health_check_at',
        'health_check_error',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'published_at' => 'datetime',
        'last_health_check_at' => 'datetime',
    ];

    /**
     * Get the streamer profile that owns the VOD.
     */
    public function streamerProfile(): BelongsTo
    {
        return $this->belongsTo(StreamerProfile::class);
    }

    /**
     * Scope a query to only include manually added VODs.
     */
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    /**
     * Scope a query to only include imported VODs.
     */
    public function scopeImported($query)
    {
        return $query->where('is_manual', false);
    }

    /**
     * Scope a query to only include healthy VODs.
     */
    public function scopeHealthy($query)
    {
        return $query->where('health_status', 'healthy');
    }

    /**
     * Scope a query to only include unhealthy VODs.
     */
    public function scopeUnhealthy($query)
    {
        return $query->where('health_status', 'unhealthy');
    }

    /**
     * Scope a query to only include unchecked VODs.
     */
    public function scopeUnchecked($query)
    {
        return $query->where('health_status', 'unchecked');
    }

    /**
     * Get the formatted duration of the VOD.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration_seconds === null) {
            return null;
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Mark the VOD as healthy.
     */
    public function markAsHealthy(): void
    {
        $this->update([
            'health_status' => 'healthy',
            'last_health_check_at' => now(),
            'health_check_error' => null,
        ]);
    }

    /**
     * Mark the VOD as unhealthy.
     */
    public function markAsUnhealthy(string $error = null): void
    {
        $this->update([
            'health_status' => 'unhealthy',
            'last_health_check_at' => now(),
            'health_check_error' => $error,
        ]);
    }

    /**
     * Check if the VOD is healthy.
     */
    public function isHealthy(): bool
    {
        return $this->health_status === 'healthy';
    }

    /**
     * Check if the VOD is unhealthy.
     */
    public function isUnhealthy(): bool
    {
        return $this->health_status === 'unhealthy';
    }

    /**
     * Check if the VOD health status is unchecked.
     */
    public function isUnchecked(): bool
    {
        return $this->health_status === 'unchecked';
    }
}