<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'podcast_id',
        'episode_id',
        'streamer_profile_id',
        'title',
        'slug',
        'content',
        'rating',
        'positive_points',
        'negative_points',
        'platform_played_on',
        'is_staff_review',
        'is_published',
        'show_on_streamer_profile',
        'show_on_podcast',
    ];

    protected $casts = [
        'is_staff_review' => 'boolean',
        'is_published' => 'boolean',
        'positive_points' => 'array',
        'negative_points' => 'array',
        'show_on_streamer_profile' => 'boolean',
        'show_on_podcast' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($review) {
            if (empty($review->slug) && !empty($review->title)) {
                $review->slug = Str::slug($review->title);
                
                // Ensure uniqueness
                $originalSlug = $review->slug;
                $count = 1;
                while (static::where('slug', $review->slug)->exists()) {
                    $review->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
        
        static::updating(function ($review) {
            if ($review->isDirty('title') && !empty($review->title)) {
                $review->slug = Str::slug($review->title);
                
                // Ensure uniqueness
                $originalSlug = $review->slug;
                $count = 1;
                while (static::where('slug', $review->slug)->where('id', '!=', $review->id)->exists()) {
                    $review->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function streamerProfile()
    {
        return $this->belongsTo(StreamerProfile::class);
    }

    public function attachedToEpisodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_review_attachments')
                    ->withPivot('attached_by')
                    ->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function comments()
    {
        return $this->hasMany(ReviewComment::class)->orderBy('created_at', 'asc');
    }

    public function scopeStaff($query)
    {
        return $query->where('is_staff_review', true);
    }

    public function scopeUser($query)
    {
        return $query->where('is_staff_review', false);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePodcast($query)
    {
        return $query->whereNotNull('podcast_id');
    }

    public function scopeNotPodcast($query)
    {
        return $query->whereNull('podcast_id');
    }

    public function scopeStreamer($query)
    {
        return $query->whereNotNull('streamer_profile_id');
    }

    public function scopeNotStreamer($query)
    {
        return $query->whereNull('streamer_profile_id');
    }

    public function scopeVisibleOnStreamerProfile($query)
    {
        return $query->whereNotNull('streamer_profile_id')
                    ->where('show_on_streamer_profile', true);
    }

    public function scopeVisibleOnPodcast($query)
    {
        return $query->whereNotNull('podcast_id')
                    ->where('show_on_podcast', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getHardwarePlayedOn()
    {
        if (!$this->platform_played_on) {
            return null;
        }
        
        return \App\Models\Product::whereIn('type', ['hardware', 'accessory'])
            ->where('slug', $this->platform_played_on)
            ->first();
    }

    public function getPositivePointsListAttribute()
    {
        if (is_string($this->positive_points)) {
            return array_filter(explode("\n", $this->positive_points));
        }
        return $this->positive_points ?? [];
    }

    public function getNegativePointsListAttribute()
    {
        if (is_string($this->negative_points)) {
            return array_filter(explode("\n", $this->negative_points));
        }
        return $this->negative_points ?? [];
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'review_likes')->withTimestamps();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isPodcastReview()
    {
        return !is_null($this->podcast_id);
    }

    public function isStreamerReview()
    {
        return !is_null($this->streamer_profile_id);
    }

    public function getAuthorDisplayNameAttribute()
    {
        if ($this->isStreamerReview()) {
            return $this->user->name . ' (' . $this->streamerProfile->channel_name . ')';
        }

        if ($this->isPodcastReview()) {
            $name = $this->user->name . ' (' . $this->podcast->name;
            
            if ($this->episode) {
                $name .= ', ' . $this->episode->display_number;
            }
            
            $name .= ')';
            return $name;
        }

        return $this->user->name;
    }

    public function getReviewTypeAttribute()
    {
        if ($this->isStreamerReview()) {
            return 'streamer';
        }

        if ($this->isPodcastReview()) {
            return 'podcast';
        }

        return $this->is_staff_review ? 'staff' : 'user';
    }

    public function getReviewContextAttribute()
    {
        if ($this->isStreamerReview()) {
            return [
                'type' => 'streamer',
                'streamer_profile' => $this->streamerProfile,
            ];
        }

        if ($this->isPodcastReview()) {
            return [
                'type' => 'podcast',
                'podcast' => $this->podcast,
                'episode' => $this->episode,
            ];
        }

        return [
            'type' => $this->is_staff_review ? 'staff' : 'user',
        ];
    }
}
