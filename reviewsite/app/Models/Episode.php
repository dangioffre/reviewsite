<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'podcast_id',
        'title',
        'slug',
        'description',
        'show_notes',
        'published_at',
        'audio_url',
        'artwork_url',
        'duration',
        'episode_number',
        'season_number',
        'episode_type',
        'is_explicit',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_explicit' => 'boolean',
        'tags' => 'array',
        'duration' => 'integer',
        'episode_number' => 'integer',
        'season_number' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($episode) {
            if (empty($episode->slug)) {
                $episode->slug = static::generateUniqueSlug($episode->title, $episode->podcast_id);
            }
        });

        static::updating(function ($episode) {
            if ($episode->isDirty('title') && empty($episode->slug)) {
                $episode->slug = static::generateUniqueSlug($episode->title, $episode->podcast_id);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships
    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function attachedReviews()
    {
        return $this->belongsToMany(Review::class, 'episode_review_attachments')
                    ->withPivot('attached_by')
                    ->withTimestamps();
    }

    public function attachedGameReviews()
    {
        return $this->belongsToMany(Review::class, 'episode_review_attachments')
                    ->withPivot('attached_by')
                    ->withTimestamps()
                    ->whereHas('product', function ($query) {
                        $query->where('type', 'game');
                    });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('episode_type', $type);
    }

    public function scopeBySeason($query, $season)
    {
        return $query->where('season_number', $season);
    }

    // Helper methods
    public function isPublished()
    {
        return $this->published_at <= now();
    }

    public function getDisplayNumberAttribute()
    {
        if ($this->season_number && $this->episode_number) {
            return "S{$this->season_number}E{$this->episode_number}";
        }
        
        if ($this->episode_number) {
            return "Episode {$this->episode_number}";
        }
        
        return null;
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return null;
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getArtworkUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : $this->podcast->logo_url;
    }

    public function getShortDescriptionAttribute()
    {
        return $this->description ? Str::limit($this->description, 150) : null;
    }

    public function getFullTitleAttribute()
    {
        $title = $this->title;
        
        if ($this->display_number) {
            $title = $this->display_number . ': ' . $title;
        }
        
        return $title;
    }

    public function canBeReviewedBy(User $user)
    {
        return $this->isPublished() && $this->podcast->isApproved();
    }

    public function hasReviewFrom(User $user)
    {
        return $this->reviews()->where('user_id', $user->id)->exists();
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating');
    }

    public function canAttachReview(Review $review, User $user)
    {
        // Check if user is a podcast team member
        if (!$this->podcast->userCanPostAsThisPodcast($user)) {
            return false;
        }

        // Check if review is by a team member
        if (!$this->podcast->userCanPostAsThisPodcast($review->user)) {
            return false;
        }

        // Check if review is for a product (not an episode review)
        if ($review->episode_id) {
            return false;
        }

        // Check if already attached
        if ($this->attachedReviews()->where('review_id', $review->id)->exists()) {
            return false;
        }

        return true;
    }

    public function attachReview(Review $review, User $user)
    {
        if (!$this->canAttachReview($review, $user)) {
            return false;
        }

        $this->attachedReviews()->attach($review->id, [
            'attached_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return true;
    }

    public function detachReview(Review $review, User $user)
    {
        // Check if user is a podcast team member
        if (!$this->podcast->userCanPostAsThisPodcast($user)) {
            return false;
        }

        $this->attachedReviews()->detach($review->id);
        return true;
    }

    public function getAvailableReviewsForUser(User $user)
    {
        if (!$this->podcast->userCanPostAsThisPodcast($user)) {
            return collect();
        }

        // Get all team members
        $teamMembers = $this->podcast->activeTeamMembers()->pluck('user_id')
                          ->push($this->podcast->owner_id)
                          ->unique();

        // Get their product reviews that aren't already attached
        return Review::whereIn('user_id', $teamMembers)
                    ->whereNull('episode_id') // Only product reviews
                    ->whereNotIn('id', $this->attachedReviews()->pluck('review_id'))
                    ->with(['product', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    // Static methods
    public static function generateUniqueSlug($title, $podcastId, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->where('podcast_id', $podcastId)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Episode type constants
    const TYPE_FULL = 'full';
    const TYPE_TRAILER = 'trailer';
    const TYPE_BONUS = 'bonus';

    public static function getTypes()
    {
        return [
            self::TYPE_FULL => 'Full Episode',
            self::TYPE_TRAILER => 'Trailer',
            self::TYPE_BONUS => 'Bonus Content',
        ];
    }
} 