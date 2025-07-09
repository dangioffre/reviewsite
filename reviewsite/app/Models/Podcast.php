<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Podcast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'rss_url',
        'logo_url',
        'website_url',
        'hosts',
        'links',
        'status',
        'verification_token',
        'verification_status',
        'last_rss_check',
        'rss_error',
        'approved_at',
        'approved_by',
        'admin_notes',
    ];

    protected $casts = [
        'hosts' => 'array',
        'links' => 'array',
        'verification_status' => 'boolean',
        'last_rss_check' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($podcast) {
            if (empty($podcast->slug)) {
                $podcast->slug = static::generateUniqueSlug($podcast->name);
            }
            if (empty($podcast->verification_token)) {
                $podcast->verification_token = static::generateUniqueToken();
            }
        });

        static::updating(function ($podcast) {
            if ($podcast->isDirty('name') && empty($podcast->slug)) {
                $podcast->slug = static::generateUniqueSlug($podcast->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function teamMembers()
    {
        return $this->hasMany(PodcastTeamMember::class);
    }

    public function activeTeamMembers()
    {
        return $this->teamMembers()->whereNotNull('accepted_at');
    }

    public function pendingTeamMembers()
    {
        return $this->teamMembers()->whereNull('accepted_at');
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function canPostReviews()
    {
        return $this->isApproved();
    }

    public function isOwner(User $user)
    {
        return $this->owner_id === $user->id;
    }

    public function isTeamMember(User $user)
    {
        return $this->activeTeamMembers()->where('user_id', $user->id)->exists();
    }

    public function userCanPostAsThisPodcast(User $user)
    {
        return $this->canPostReviews() && ($this->isOwner($user) || $this->isTeamMember($user));
    }

    public function needsRssCheck()
    {
        return is_null($this->last_rss_check) || 
               $this->last_rss_check->lt(now()->subHours(6));
    }

    public function hasRssError()
    {
        return !is_null($this->rss_error);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function getLogoUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    // Static methods
    public static function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public static function generateUniqueToken()
    {
        do {
            $token = 'podcast-verify-' . Str::random(32);
        } while (static::where('verification_token', $token)->exists());

        return $token;
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
} 